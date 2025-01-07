<?php
// Include database connection
require_once($_SERVER['DOCUMENT_ROOT']."/includes/config.php");
session_start();  // Start session to store login data

// Initialize the Database
$db = initializeDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Prepare and execute SQL query to get user data based on the email provided
    $stmt = $db->prepare("SELECT id, name, password, administrator FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // If a user with the provided email exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Store user data in session
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['administrator'] = $user['administrator']; // Store administrator status

            // If "Remember me" is checked, set a cookie for 30 days
            if (isset($_POST['remember_me']) && $_POST['remember_me'] == 1) {
                setcookie('loggedIn', true, time() + (86400 * 30), "/"); // 86400 seconds = 1 day
            } else {
                setcookie('loggedIn', true, 0, "/");  // Session cookie
            }

            // Redirect the user to the homepage or a specific page after successful login
            header("Location: /index.php");
            exit;
        } else {
            // Incorrect password, return error
            $error = "Invalid password.";
        }
    } else {
        // No user found with the provided email
        $error = "No account found with that email address.";
    }

    $stmt->close();
    $db->close();
}

// If there's an error, redirect back to the modal with an error message
if (isset($error)) {
    $_SESSION['error'] = $error;
    $_SESSION['keep_modal_open'] = true; // Add this flag to keep the modal open
    header("Location: /index.php");
    exit;
}

$keep_modal_open = false;

// Check if the keep_modal_open flag is set
if (isset($_SESSION['keep_modal_open']) && $_SESSION['keep_modal_open'] === true) {
    $keep_modal_open = true;
    unset($_SESSION['keep_modal_open']); // Clear the flag after handling
}
?>
