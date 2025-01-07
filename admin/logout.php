<?php
session_start();

// Unset all of the session variables
$_SESSION = array();

// If cookies were set, clear them
if (isset($_COOKIE['loggedIn'])) {
    setcookie('loggedIn', '', time() - 3600, '/'); // Expire cookie
}

// Destroy the session
session_destroy();

// Redirect to the homepage or login page after logging out
header("Location: /index.php");
exit;
?>
