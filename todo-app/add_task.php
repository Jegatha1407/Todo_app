<?php
session_start();
include 'db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// When user submits the form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO tasks (user_id, title, description, due_date, priority, status) 
            VALUES ('$user_id', '$title', '$description', '$due_date', '$priority', 'Pending')";
    mysqli_query($conn, $sql);

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Task</title>
<link rel="stylesheet" href="assets/style.css">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #74ABE2, #5563DE);
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}
.container {
  background: white;
  padding: 30px;
  border-radius: 20px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
  width: 400px;
}
.container h2 {
  text-align: center;
  color: #333;
}
form {
  display: grid;
  gap: 15px;
}
input, textarea, select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 10px;
  font-size: 14px;
}
button {
  background: linear-gradient(90deg, #4B6CB7, #182848);
  color: white;
  border: none;
  padding: 12px;
  border-radius: 10px;
  font-size: 16px;
  cursor: pointer;
  transition: 0.3s;
}
button:hover {
  background: linear-gradient(90deg, #182848, #4B6CB7);
}
a {
  display: block;
  text-align: center;
  color: #333;
  margin-top: 15px;
  text-decoration: none;
}
</style>
</head>
<body>

<div class="container">
  <h2>Add New Task</h2>
  <form action="" method="POST">
    <input type="text" name="title" placeholder="Task Title" required>
    <textarea name="description" placeholder="Task Description" rows="3" required></textarea>
    <input type="date" name="due_date" required>
    <select name="priority" required>
      <option value="">Select Priority</option>
      <option value="Low">Low</option>
      <option value="Medium">Medium</option>
      <option value="High">High</option>
    </select>
    <button type="submit">Add Task</button>
  </form>
  <a href="index.php">← Back to Tasks</a>
</div>

</body>
</html>
