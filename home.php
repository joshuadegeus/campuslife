<?php

if (isset($_GET['campus'])) {
    $campus_id = htmlspecialchars($_GET['campus']);
    echo "Selected Campus ID: " . $campus_id;
} else {
    echo "No campus selected.";
}
?>