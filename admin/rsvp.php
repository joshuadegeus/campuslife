<?php
session_start();

// Enable error reporting (for debugging only; remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json'); // Ensure the response is JSON

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit;
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $event_id = $input['event_id'] ?? null;
    $user_id = $_SESSION['user_id'];

    if (!$event_id) {
        echo json_encode(['success' => false, 'error' => 'Event ID is required.']);
        exit;
    }

    // Include database connection
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/config.php");
    $db = initializeDB();

    if (!$db) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed.']);
        exit;
    }

    // Check if RSVP already exists to prevent duplicates
    $stmt = $db->prepare("SELECT * FROM event_user_map WHERE event_id = ? AND user_id = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $db->error]);
        exit;
    }

    $stmt->bind_param("ii", $event_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'You have already RSVP’d for this event.']);
    } else {
        // Insert RSVP into the database
        $stmt = $db->prepare("INSERT INTO event_user_map (event_id, user_id) VALUES (?, ?)");
        if (!$stmt) {
            echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $db->error]);
            exit;
        }

        $stmt->bind_param("ii", $event_id, $user_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Execute failed: ' . $stmt->error]);
        }
    }

    $stmt->close();
    $db->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>



