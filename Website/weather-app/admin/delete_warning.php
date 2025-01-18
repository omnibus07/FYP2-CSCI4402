<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access";
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Warning ID not provided";
    header("Location: manage_warnings.php");
    exit();
}

$warning_id = intval($_GET['id']);

$conn = new mysqli('localhost', 'root', '', 'weather_app');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("DELETE FROM weather_warnings WHERE id = ?");
$stmt->bind_param("i", $warning_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $_SESSION['success'] = "Weather warning deleted successfully";
    } else {
        $_SESSION['error'] = "Warning not found";
    }
} else {
    $_SESSION['error'] = "Error deleting warning: " . $conn->error;
}

$stmt->close();
$conn->close();

header("Location: manage_warnings.php");
exit();
?>