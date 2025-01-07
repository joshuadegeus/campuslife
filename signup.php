<?php
// Include Configuration File.
require_once($_SERVER['DOCUMENT_ROOT']."/includes/config.php");

// Initialize the Database.
$db = initializeDB();

startHTML("Create an account");

includeHeader();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if the email ends with 'unr.edu'
    if (substr($email, -7) == 'unr.edu') {
        // Check if the email already exists in the database
        $stmt = $db->prepare("SELECT COUNT(*) FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($emailCount);
        $stmt->fetch();
        $stmt->close();

        if ($emailCount > 0) {
            $error = "An account with this email already exists.";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare an SQL statement to insert the user into the database
            $stmt = $db->prepare("INSERT INTO user (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            if ($stmt->execute()) {
                // Log the user in by storing their details in the session
                session_start();
                $_SESSION['user_id'] = $stmt->insert_id; // Get the ID of the newly inserted user
                $_SESSION['user_name'] = $name;
                $_SESSION['administrator'] = 0; // Default value; adjust if necessary
                
                // Set a cookie that indicates the user is logged in
                setcookie("loggedIn", $email, time() + (86400 * 30), "/"); // 30 days expiration

                // Redirect the user to the index page
                header("Location: /index.php");
                exit();
            } else {
                $error = "Error creating account. Please try again.";
            }

            $stmt->close();
        }
    } else {
        $error = "Only unr.edu emails are allowed.";
    }
}

?>

<!-- HTML Goes Here. -->
<main>
<div class="container col-xl-10 col-xxl-8 px-4 py-5">
    <div class="row align-items-center g-lg-5 py-5">
      <div class="col-lg-7 text-center text-lg-start">
        <h1 class="display-4 fw-bold lh-1 text-body-emphasis mb-3">Create an account</h1>
        <p class="col-lg-10 fs-4">Sign up for an account with Campus Life. It's free, easy, and helps you stay organized with all your events in one place.</p>
      </div>
      <div class="col-md-10 mx-auto col-lg-5">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        <form method="POST" class="p-4 p-md-5 border rounded-3 bg-body-tertiary">
          <div class="form-floating mb-3">
              <input type="text" name="name" class="form-control" id="floatingname" placeholder="Name" required>
              <label for="floatingname">Name</label>            
          </div>
          <div class="form-floating mb-3">
            <input type="email" name="email" class="form-control" id="floatingInput" placeholder="name@example.com" required>
            <label for="floatingInput">Email address*</label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
            <label for="floatingPassword">Password*</label>
          </div>
          <div class="checkbox mb-3">
            <label>
              <input type="checkbox" value="remember-me"> Remember me
            </label>
          </div>
          <button class="w-100 btn btn-lg btn-primary" type="submit">Sign up</button>
        </form>
      </div>
    </div>
  </div>
</main>

<?php
endHTML();
?>