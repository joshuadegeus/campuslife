<?php

// Initialize the database, this should be a function in a config file.
$host = "sql210.infinityfree.com";
$user = "if0_37292991";
$pass = "jFyqDWgD6DcPd";
$db = "if0_37292991_campus_life";

$db=mysqli_connect($host, $user,  $pass, $db) or die ('I cannot connect to the database because:' . mysqli_error($db));

$campuses = array(); // Initialize empty array for list of schools.

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
    <title>Select Your School</title>
</head>
<body>

    <h1>Select Your School</h1>
    <form action="your_form_processing_script.php" method="POST">
        <label for="campus-select">Choose a campus:</label>
        <select name="campus" id="campus-select" required>
            <option value="" disabled selected>Please select your school below.</option>
            <option value="" disabled selected>--------------------------------</option>
            <?php
            // Loop through campuses array and generate the options for the dropdown.
            foreach ($campuses as $id => $campus) {
                echo "<option value='" . htmlspecialchars($id) . "'>" . htmlspecialchars($campus['name']) . "</option>";
            }
            ?>
        </select>

        <button type="submit">Submit</button>
    </form>

</body>
</html>
