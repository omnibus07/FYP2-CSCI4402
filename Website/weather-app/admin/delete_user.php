<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access";
    header("Location: index.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "User ID not provided";
    header("Location: manage_users.php");
    exit();
}

$user_id = intval($_GET['id']);

// Prevent admin from deleting themselves
if ($user_id === $_SESSION['user_id']) {
    $_SESSION['error'] = "You cannot delete your own account";
    header("Location: manage_users.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'weather_app');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare statement to prevent SQL injection
$stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $_SESSION['success'] = "User deleted successfully";
    } else {
        $_SESSION['error'] = "User not found or cannot delete admin";
    }
} else {
    $_SESSION['error'] = "Error deleting user: " . $conn->error;
}

$stmt->close();
$conn->close();

header("Location: manage_users.php");
exit();
?>