<?php
session_start();

// Check if the request is valid
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

// Get the input data
$input = json_decode(file_get_contents('php://input'), true);
$event_id = $input['event_id'] ?? null;
$feedback = $input['feedback'] ?? null;
$anonymous = $input['anonymous'] ?? false;

// Validate input
if (!$event_id || !$feedback) {
    echo json_encode(['success' => false, 'error' => 'Event ID and feedback are required.']);
    exit;
}

// Include database configuration
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/config.php");
$db = initializeDB();

// Prepare the query
if ($anonymous || !isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("INSERT INTO feedback (event_id, feedback) VALUES (?, ?)");
    $stmt->bind_param("is", $event_id, $feedback);
} else {
    $user_id = $_SESSION['user_id'];
    $stmt = $db->prepare("INSERT INTO feedback (event_id, feedback, user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $event_id, $feedback, $user_id);
}

// Execute the query
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$db->close();
?>