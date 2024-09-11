<?php
// Test index.php for web hosting

// Display a welcome message
echo "<h1>Test Page</h1>";
echo "<p>If you see this message, your PHP is working!</p>";

// Display the current date and time
echo "<p>Current Server Time: " . date("Y-m-d H:i:s") . "</p>";

// Display the server IP
echo "<p>Server IP: " . $_SERVER['SERVER_ADDR'] . "</p>";

// Display PHP information (Uncomment the following line if needed)
// phpinfo();

?>
