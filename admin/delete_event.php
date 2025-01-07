<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access.']);
    exit;
}

// Check if the request is valid
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $event_id = $input['event_id'] ?? null;

    if (!$event_id) {
        echo json_encode(['success' => false, 'error' => 'Event ID is required.']);
        exit;
    }

    // Include database configuration
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/config.php");
    $db = initializeDB();

    // Begin transaction
    $db->begin_transaction();

    try {
        // Delete from event_user_map
        $stmt = $db->prepare("DELETE FROM event_user_map WHERE event_id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $stmt->close();

        // Delete from feedback
        $stmt = $db->prepare("DELETE FROM feedback WHERE event_id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $stmt->close();

        // Delete from event
        $stmt = $db->prepare("DELETE FROM event WHERE id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $db->commit();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Rollback transaction if something goes wrong
        $db->rollback();
        echo json_encode(['success' => false, 'error' => 'Failed to delete event: ' . $e->getMessage()]);
    }

    $db->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
