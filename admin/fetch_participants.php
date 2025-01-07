<?php
session_start();
header('Content-Type: application/json');

if (!isset($_GET['event_id'])) {
    echo json_encode(['success' => false, 'error' => 'Event ID is required.']);
    exit;
}

$event_id = (int)$_GET['event_id'];

// Include Configuration File
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/config.php");

$db = initializeDB();

$stmt = $db->prepare("
    SELECT u.name, u.email 
    FROM event_user_map eum
    JOIN user u ON eum.user_id = u.id
    WHERE eum.event_id = ?
");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

$participants = [];
while ($row = $result->fetch_assoc()) {
    $participants[] = $row;
}

$stmt->close();
$db->close();

echo json_encode(['success' => true, 'participants' => $participants]);
?>
