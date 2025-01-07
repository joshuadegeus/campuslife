<?php

session_start(); // Start session at the beginning

?>

<!-- footer -->
  <footer class="py-3 my-4">
    <ul class="nav justify-content-center border-bottom pb-3 mb-3">
     <li class="nav-item"><a href="/" class="nav-link px-2 text-body-secondary">Home</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Features</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Pricing</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">FAQs</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">About</a></li>
      <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Login</a></li>
      <li class="nav-item"><a href="/signup.php" class="nav-link px-2 text-body-secondary">Sign Up</a></li>
    </ul>
    </ul>
    <p class="text-center text-body-secondary">&copy;<span id="year"></span> Campus Life</p>
  </footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <!-- Set JavaScript variable for logged-in status -->
        <script>
          window.isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
          console.log("Logged-in status:", window.isLoggedIn);
        </script>
        <script src="/includes/js/global.js"></script>
    </body>
</html>