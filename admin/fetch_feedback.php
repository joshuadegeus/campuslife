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

$stmt = $db->prepare("SELECT feedback, created_at FROM feedback WHERE event_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

$feedback = [];
while ($row = $result->fetch_assoc()) {
    $feedback[] = $row;
}

$stmt->close();
$db->close();

echo json_encode(['success' => true, 'feedback' => $feedback]);
?>