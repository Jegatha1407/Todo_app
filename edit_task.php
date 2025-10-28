~~~{"id":"90030","variant":"standard/chat_message","title":"edit_task.php"}
<?php
require_once 'db.php';
require_login();

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

// Fetch current task
$stmt = $mysqli->prepare("SELECT * FROM tasks WHERE id=? AND user_id=?");
$stmt->bind_param('ii', $id, $_SESSION['user_id']);
$stmt->execute();
$res = $stmt->get_result();
$task = $res->fetch_assoc();

if (!$task) {
    header('Location: index.php');
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc = trim($_POST['description']);
    $due = $_POST['due_date'];
    $priority = $_POST['priority'];

    $up = $mysqli->prepare("UPDATE tasks SET title=?, description=?, due_date=?, priority=? WHERE id=? AND user_id=?");
    $up->bind_param('ssssii', $title, $desc, $due, $priority, $id, $_SESSION['user_id']);
    $up->execute();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Task | To-Do App</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body class="auth-body">
<div class="auth-container">
  <div class="auth-card">
    <h1 class="title">Edit Task 📝</h1>
    <form method="post" class="auth-form">
      <label>Title</label>
      <input type="text" name="title" value="<?=htmlspecialchars($task['title'])?>" required>

      <label>Description</label>
      <textarea name="description"><?=htmlspecialchars($task['description'])?></textarea>

      <label>Due Date</label>
      <input type="date" name="due_date" value="<?=$task['due_date']?>">

      <label>Priority</label>
      <select name="priority">
        <option <?=$task['priority']=='Low'?'selected':''?>>Low</option>
        <option <?=$task['priority']=='Medium'?'selected':''?>>Medium</option>
        <option <?=$task['priority']=='High'?'selected':''?>>High</option>
      </select>

      <button type="submit" class="btn-primary">Save Changes</button>
      <a href="index.php" class="btn-secondary">Cancel</a>
    </form>
  </div>
</div>
</body>
</html>
