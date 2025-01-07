<?php

session_start(); // Start session at the beginning

$keep_modal_open = false;

// Check if the keep_modal_open flag is set
if (isset($_SESSION['keep_modal_open']) && $_SESSION['keep_modal_open'] === true) {
    $keep_modal_open = true;
    unset($_SESSION['keep_modal_open']); // Clear the flag after handling
}

?>

<header>
  <nav class="navbar navbar-expand-sm border-bottom" aria-label="Navigation Bar">
    <div class="container">
      <a class="navbar-brand" href="index.php">Campus Life</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbar">
        

        
          <?php if (isset($_COOKIE['loggedIn']) && isset($_SESSION['user_name'])): ?>
            
              <div class="navbar-text mx-auto">
                Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?>!
              </div>
            
            
        <ul class="navbar-nav ms-auto mb-2 mb-sm-0"> <!-- ms-auto here for right alignment -->    
            <!-- Sign Out button -->
            <li class="nav-item">
              <a class="nav-link" href="admin/logout.php">Sign Out</a>
            </li>

            <?php if (isset($_SESSION['administrator']) && $_SESSION['administrator'] == 1): ?>
              <!-- Manage Events button (only for admins) -->
              <li class="nav-item">
                <a href="admin-home.php">
                  <button type="button" class="btn btn-primary">
                    Manage Events
                  </button>
                </a>
              </li>
            <?php endif; ?>

          <?php else: ?>
            <!-- Sign In button (only if not logged in) -->
        <ul class="navbar-nav ms-auto mb-2 mb-sm-0"> <!-- ms-auto here for right alignment -->     
            <li class="nav-item">
              <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#modalSignin">Sign In</a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
</header>

<!-- SIGN IN MODAL -->
<div class="modal fade" id="modalSignin" tabindex="-1" aria-labelledby="ModalSignInLabel" aria-hidden="true" data-keep-open="<?= $keep_modal_open ? 'true' : 'false'; ?>">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <span class="h1 modal-title fs-5" id="ModalSignInLabel">Sign In</span>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form method="POST" action="admin/login.php">
          <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
              <?= htmlspecialchars($_SESSION['error']); ?>
            </div>
          <?php endif; ?>

          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
            <label for="email">Email address</label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            <label for="password">Password</label>
          </div>
          <div class="checkbox mb-3">
            <label>
              <input type="checkbox" name="remember_me" value="1"> Remember me
            </label>
          </div>
          <button class="w-100 btn btn-lg btn-primary" type="submit">Sign In</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- /modal -->