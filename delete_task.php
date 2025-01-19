<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$taskID = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM Task WHERE taskID = ?");
$stmt->bind_param("i", $taskID);

if ($stmt->execute()) {
    header("Location: dashboard.php");
} else {
    echo "Error deleting task.";
}
?>
