<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Database connection
$db = new mysqli('localhost', 'root', '', 'weather_app');

if ($db->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Get and sanitize input
$username = $db->real_escape_string($_POST['username']);
$email = $db->real_escape_string($_POST['email']);
$password = $_POST['password'];

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode(['success' => false, 'message' => 'Invalid email format']));
}

// Check if email already exists
$stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die(json_encode(['success' => false, 'message' => 'Email already registered']));
}

// Check if username already exists
$stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die(json_encode(['success' => false, 'message' => 'Username already taken']));
}

// Hash password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insert new user
$stmt = $db->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?,'user')");
$stmt->bind_param("sss", $username, $email, $password_hash);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Registration successful']);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed']);
}

$stmt->close();
$db->close();
?>