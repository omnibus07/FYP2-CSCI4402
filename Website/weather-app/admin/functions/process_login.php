<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

echo 'hello 123';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo 'hello 1233';
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    $conn = new mysqli('localhost', 'root', '', 'weather_app');
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    echo 'hello 321';
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();


        if (password_verify($password, hash: $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_avatar'] = $user['profile_photo'];
            
            if (isset($_POST['remember'])) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (86400 * 30), "/");
                
                $stmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $stmt->bind_param("si", $token, $user['id']);
                $stmt->execute();
            }
            
            header("Location: ../manage_users.php");
            exit();
        }
    }
    echo 'hello 22';
    $_SESSION['error'] = "Invalid email or password";
    header("Location: ../sign-in.php");
    exit();
}