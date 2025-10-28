<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ensure both ID and status are provided
if (isset($_GET['id']) && isset($_GET['status'])) {
    $task_id = intval($_GET['id']);
    $status = $_GET['status'] === 'Completed' ? 'Completed' : 'Pending';

    // Update only if the task belongs to the user
    $sql = "UPDATE tasks SET status='$status' WHERE id='$task_id' AND user_id='$user_id'";
    mysqli_query($conn, $sql);
}

// Redirect back to main dashboard
header("Location: index.php");
exit();
?>
