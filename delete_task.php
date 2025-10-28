<?php
session_start();
include 'db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if task ID is provided
if (isset($_GET['id'])) {
    $task_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Ensure the task belongs to the logged-in user
    $sql = "DELETE FROM tasks WHERE id = '$task_id' AND user_id = '$user_id'";
    mysqli_query($conn, $sql);
}

header("Location: index.php");
exit();
?>
