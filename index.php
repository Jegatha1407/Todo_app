<?php
// index.php - complete working file (copy & paste)

include 'db.php'; // ensure this file defines $conn (mysqli) and starts no output

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = (int) $_SESSION['user_id'];

// === Handle Add Task ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $title = trim($_POST['title'] ?? '');
    if ($title !== '') {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param("is", $user_id, $title);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: index.php');
    exit();
}

// === Handle Update Task (title + status) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $task_id = intval($_POST['task_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $status = $_POST['status'] ?? 'Pending';
    if ($task_id > 0 && $title !== '') {
        $stmt = $conn->prepare("UPDATE tasks SET title = ?, status = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $title, $status, $task_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: index.php');
    exit();
}

// === Handle Quick Status Change via GET ===
if (isset($_GET['change_status']) && isset($_GET['task_id'])) {
    $task_id = intval($_GET['task_id']);
    $new_status = $_GET['change_status'];
    // Validate status value
    $allowed = ['Pending', 'In Progress', 'Completed'];
    if (in_array($new_status, $allowed, true) && $task_id > 0) {
        $stmt = $conn->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $new_status, $task_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: index.php');
    exit();
}

// === Handle Delete via GET ===
if (isset($_GET['delete'])) {
    $task_id = intval($_GET['delete']);
    if ($task_id > 0) {
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $task_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: index.php');
    exit();
}

// === Fetch tasks for display ===
$tasks = [];
$stmt = $conn->prepare("SELECT id, title, status, created_at FROM tasks WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $tasks[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>My Tasks · ToDo App</title>
<style>
/* ---------- base ---------- */
*{box-sizing:border-box}
body{
  margin:0;
  font-family:Poppins, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  background: linear-gradient(135deg,#2bbf9b,#3aa3d3);
  color:#fff;
  min-height:100vh;
  display:flex;
  flex-direction:column;
  align-items:center;
  padding:28px 18px;
}

/* ---------- header & add ---------- */
.header {
  width:100%;
  max-width:1100px;
  text-align:center;
  margin-bottom:22px;
}
.header h1{font-size:2.1rem; margin:0 0 6px}
.header p{margin:0;color:rgba(255,255,255,0.9)}

.topbar {
  width:100%;
  max-width:1100px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:20px;
}
.logout-btn{
  background:linear-gradient(90deg,#ff6b6b,#ff416c);
  padding:8px 14px;border-radius:10px;color:#fff;text-decoration:none;font-weight:600;
  box-shadow:0 6px 18px rgba(0,0,0,0.12);
}
.logout-btn:hover{transform:translateY(-2px)}

/* add form */
.add-form{
  width:100%;
  max-width:1100px;
  display:flex;
  gap:12px;
  align-items:center;
  padding:18px;
  border-radius:14px;
  background: rgba(255,255,255,0.08);
  box-shadow: 0 8px 22px rgba(0,0,0,0.12);
  margin-bottom:28px;
}
.add-form input{
  flex:1;
  padding:12px 16px;
  border-radius:10px;
  border: none;
  font-size:16px;
  outline:none;
}
.add-form button{
  padding:11px 18px;border-radius:10px;border:none;font-weight:700;
  background:linear-gradient(90deg,#4facfe,#00f2fe); color:#062b3a; cursor:pointer;
}

/* ---------- grid ---------- */
.task-container{
  width:100%;
  max-width:1100px;
  display:grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap:22px;
  align-items:start;
  margin-bottom:60px;
}

/* card */
.task-card{
  background: linear-gradient(135deg, rgba(255,255,255,0.06), rgba(255,255,255,0.03));
  border-radius:16px;
  padding:18px;
  min-height:120px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.12);
  transition: transform .18s ease, box-shadow .18s ease;
  position:relative;
}
.task-card:hover{
  transform: translateY(-6px);
  box-shadow: 0 18px 40px rgba(0,0,0,0.18);
}
.task-title{
  font-size:1.15rem;
  margin:6px 0 10px;
  color: #fff;
}

/* status badge */
.status-badge{
  display:inline-block;
  padding:6px 10px;
  border-radius:12px;
  font-weight:700;
  font-size:0.9rem;
  margin-bottom:10px;
}
.status-pending{background:#f39c12;color:#fff;}
.status-inprogress{background:#3498db;color:#fff;}
.status-completed{background:#2ecc71;color:#fff;}

/* actions area */
.task-actions{
  display:flex;
  gap:8px;
  flex-wrap:wrap;
  margin-top:12px;
}
.btn{
  border:0;padding:8px 12px;border-radius:10px;font-weight:700;cursor:pointer;
  box-shadow: 0 8px 16px rgba(0,0,0,0.08);
}
.btn-edit{background:linear-gradient(90deg,#8be9a8,#3bd1c2);color:#042;}
.btn-delete{background:linear-gradient(90deg,#ff7b7b,#ff5a5a);color:#420;}
.btn-status{background:linear-gradient(90deg,#9a8afe,#7de0ff);color:#042}

/* small forms inside card */
.update-form input[type="text"], .update-form select {
  width:100%;
  padding:8px 10px;
  margin-bottom:8px;
  border-radius:8px;
  border:none;
  outline:none;
  font-size:1rem;
}

/* responsive tweaks */
@media (max-width:420px){
  .add-form{flex-direction:column;gap:10px}
}
</style>
</head>
<body>

  <div class="topbar">
    <div class="header">
      <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
      <p>Manage tasks — Pending · In Progress · Completed</p>
    </div>
    <a class="logout-btn" href="logout.php">Logout</a>
  </div>

  <!-- Add new task -->
  <form class="add-form" method="post" action="index.php">
    <input type="text" name="title" placeholder="Add a new task..." required />
    <button name="add" type="submit">Add</button>
  </form>

  <!-- Tasks grid -->
  <div class="task-container">
    <?php
      // render each task as a card
      foreach ($tasks as $task) {
        $tid = (int)$task['id'];
        $title = htmlspecialchars($task['title']);
        $status = $task['status'] ?? 'Pending';
        // class for styling badges (normalize)
        $statusClass = 'status-pending';
        if ($status === 'In Progress') $statusClass = 'status-inprogress';
        if ($status === 'Completed') $statusClass = 'status-completed';
    ?>
      <div class="task-card" id="task-<?php echo $tid; ?>">
        <span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>

        <?php
        // If ?edit=ID requested and matches this task, show inline update form
        if (isset($_GET['edit']) && intval($_GET['edit']) === $tid) {
        ?>
          <form class="update-form" method="post" action="index.php">
            <input type="hidden" name="task_id" value="<?php echo $tid; ?>" />
            <input type="text" name="title" value="<?php echo $title; ?>" required />
            <select name="status" required>
              <option value="Pending" <?php if ($status === 'Pending') echo 'selected'; ?>>Pending</option>
              <option value="In Progress" <?php if ($status === 'In Progress') echo 'selected'; ?>>In Progress</option>
              <option value="Completed" <?php if ($status === 'Completed') echo 'selected'; ?>>Completed</option>
            </select>
            <div class="task-actions">
              <button class="btn btn-edit" type="submit" name="update">Save</button>
              <a class="btn btn-delete" href="index.php">Cancel</a>
            </div>
          </form>
        <?php
        } else {
          // normal card view
        ?>
          <div class="task-title"><?php echo $title; ?></div>

          <div class="task-actions">
            <!-- quick status change links -->
            <a class="btn btn-status" href="index.php?change_status=Pending&task_id=<?php echo $tid; ?>">Pending</a>
            <a class="btn btn-status" href="index.php?change_status=In%20Progress&task_id=<?php echo $tid; ?>">In Progress</a>
            <a class="btn btn-status" href="index.php?change_status=Completed&task_id=<?php echo $tid; ?>">Done</a>

            <!-- edit and delete -->
            <a class="btn btn-edit" href="index.php?edit=<?php echo $tid; ?>">Edit</a>
            <a class="btn btn-delete" href="index.php?delete=<?php echo $tid; ?>" onclick="return confirm('Delete this task?')">Delete</a>
          </div>
        <?php } ?>
      </div>
    <?php } ?>
  </div>

</body>
</html>
