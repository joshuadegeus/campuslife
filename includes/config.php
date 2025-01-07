<?php

function initializeDB() {

    // Define Database Connection Values.
    $host = "sql300.infinityfree.com";
    $user = "if0_37935819";
    $pass = "TdEWO9brWNC";
    $db = "if0_37935819_campus_life";

    // Connect to Database.
    $db=mysqli_connect($host, $user,  $pass, $db) or die ('I cannot connect to the database because:' . mysqli_error($db));

    // Return DB Connection
    return $db;
}

function startHTML($pageTitle) {
    include($_SERVER['DOCUMENT_ROOT']."/includes/starthtml.php");
}

function includeHeader() {
    include($_SERVER['DOCUMENT_ROOT']."/includes/header.php");
}

function endHTML() {
    include($_SERVER['DOCUMENT_ROOT']."/includes/endhtml.php");
}

?>