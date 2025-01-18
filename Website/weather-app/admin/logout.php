<?php
// logout.php
session_start();
session_destroy();
session_start(); // Restart session for flash message
$_SESSION['message'] = 'Successfully logged out.';
header("Location: sign-in.php");
exit();
?>