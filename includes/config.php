<?php

function initializeDB() {

    // Define Database Connection Values.
    $host = "sql210.infinityfree.com";
    $user = "if0_37292991";
    $pass = "jFyqDWgD6DcPd";
    $db = "if0_37292991_campus_life";

    // Connect to Database.
    $db=mysqli_connect($host, $user,  $pass, $db) or die ('I cannot connect to the database because:' . mysqli_error($db));

    // Return DB Connection
    return $db;
}
?>