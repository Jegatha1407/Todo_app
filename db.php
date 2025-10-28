<?php
session_start();

$host = "127.0.0.1:3307";  // or "localhost" if no custom port
$user = "root";
$pass = "";
$dbname = "todo_db"; // ✅ make sure this matches your phpMyAdmin name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
