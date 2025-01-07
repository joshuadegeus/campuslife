<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $event_id = $input['event_id'] ?? null;
    $user_id = $_SESSION['user_id'];

    if (!$event_id) {
        echo json_encode(['success' => false, 'error' => 'Event ID is required.']);
        exit;
    }

    // Include database configuration
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/config.php");
    $db = initializeDB();

    // Delete the RSVP entry
    $stmt = $db->prepare("DELETE FROM event_user_map WHERE event_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $event_id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $stmt->error]);
    }

    $stmt->close();
    $db->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>




