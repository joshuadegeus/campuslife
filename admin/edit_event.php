<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized.']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
$event_id = $input['event_id'] ?? null;
$title = trim($input['title'] ?? '');
$description = trim($input['description'] ?? '');
$location = trim($input['location'] ?? '');
$event_date = trim($input['event_date'] ?? '');

if (!$event_id || !$title || !$description || !$location || !$event_date) {
    echo json_encode(['success' => false, 'error' => 'All fields are required.']);
    exit;
}

// Include database configuration
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/config.php");
$db = initializeDB();

// Update the event in the database
$stmt = $db->prepare("UPDATE event SET title = ?, description = ?, location = ?, event_time = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("ssssii", $title, $description, $location, $event_date, $event_id, $_SESSION['user_id']);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update event: ' . $stmt->error]);
}

$stmt->close();
$db->close();