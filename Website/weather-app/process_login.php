<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Connect to database
    $conn = new mysqli('localhost', 'root', '', 'weather_app');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get and sanitize email
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Debug: Show the input values
    // echo "Email being checked: " . $email . "<br>";
    // echo "Password provided: " . $password . "<br>";

    // Show the SQL query that will be executed
    $sql = "SELECT * FROM users WHERE email = '$email'";
    // echo "SQL Query being executed: " . $sql . "<br><br>";

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Debug: Show number of rows returned
    // echo "Number of rows found: " . $result->num_rows . "<br>";

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Debug: Show all user data except password_hash
        // echo "User data found:<br>";
        // echo "ID: " . $user['id'] . "<br>";
        // echo "Username: " . $user['username'] . "<br>";
        // echo "Role: " . $user['role'] . "<br>";
        
        // Debug: Show the stored hash and generated hash
        // echo "<br>Stored password hash: " . $user['password_hash'] . "<br>";
        // echo "Generated hash from input: " . password_hash($password, PASSWORD_DEFAULT) . "<br>";
        
        // Debug: Show password verification result
        $verification_result = password_verify($password, $user['password_hash']);
        // echo "Password verification result: " . ($verification_result ? "TRUE" : "FALSE") . "<br>";

        if ($verification_result) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_avatar'] = $user['profile_photo'];
            echo json_encode(['success' => true, 'message' => 'Login successful!']);
            // Redirect to refresh the current page
        } else {
            echo json_encode(['success' => false, 'message' => 'Password verification failed!']);
        }
    } else {
        echo "<br>No user found with this email!";
    }

    $stmt->close();
    $conn->close();
}