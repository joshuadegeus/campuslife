<?php

// Include Configuaration File.
require_once($_SERVER['DOCUMENT_ROOT']."/includes/config.php");

// Initialize the Database.
$db = initializeDB();


$campuses = array(); // Initialize Empty Array for List of Schools.

$query = "SELECT *
          FROM campus
          ORDER BY name";

$result = mysqli_query($db, $query);
while ($a_row = mysqli_fetch_assoc($result)) {
    $campuses[$a_row['id']] = $a_row;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select School</title>
</head>
<body>

    <h1>Select Your School</h1>
    <form action="home.php" method="GET">
        <label for="campus-select">Choose a campus:</label>
        <select name="campus" id="campus-select" required>
            <option value="" disabled selected>Please select your school below.</option>
            <option value="" disabled>--------------------------------------------</option>
            <?php
            // Loop Through Campuses Array and Generate the Options for the Dropdown.
            foreach ($campuses as $id => $campus) {
                echo "<option value='" . htmlspecialchars($id) . "'>" . htmlspecialchars($campus['name']) . "</option>";
            }
            ?>
        </select>

        <button type="submit">Submit</button>
    </form>

</body>
</html>
