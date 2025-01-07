<?php
// Start Session
session_start();

// Include Configuration File.
require_once($_SERVER['DOCUMENT_ROOT']."/includes/config.php");

// Initialize the Database.
$db = initializeDB();

$user_id = $_SESSION['user_id'];

// Check if the form is submitted to create an event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'create' && $user_id) {
    // Retrieve form data
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = trim($_POST['event_date']);
    $location = trim($_POST['location']);

    // Prepare and execute SQL insert query
    $stmt = $db->prepare("INSERT INTO event (title, description, location, event_time, user_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $title, $description, $location, $event_date, $user_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to refresh the page
    header("Location: admin-home.php");
    exit();
}

// Start the HTML layout for the admin home page.
startHTML("Manage Events");

includeHeader(); 

?>

<!-- HTML content starts here -->

<main class="d-flex flex-nowrap">

    <div class="container mt-4">
        <h2>Create New Event</h2>
            <form action="admin-home.php" method="POST">
                <input type="hidden" name="action" value="create">
                <div class="form-group">
                    <label for="title">Event Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="description">Event Description</label>
                    <textarea class="form-control" id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="event_date">Event Date</label>
                    <input type="datetime-local" class="form-control" id="event_date" name="event_date" required>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" class="form-control" id="location" name="location" required>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Create Event</button>
            </form>

        <!-- <hr>

        <h2>My Events</h2>
        <p>View and manage events you have created.</p>
        This is where a list of events created by the admin would go 
        -->
        <hr>

        <h2>My Events</h2>

        <?php
        // Fetch events for the logged-in user
            $stmt = $db->prepare("SELECT id, title, description, location, event_time FROM event WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if there are any events
            if ($result->num_rows > 0) {
                // Loop through each event and display it in a card
                while ($row = $result->fetch_assoc()) {
                    $event_id = $row['id'];
                    $title = htmlspecialchars($row['title']);
                    $description = htmlspecialchars($row['description']);
                    $location = htmlspecialchars($row['location']);
                    $event_time = date("F j, Y, g:i a", strtotime($row['event_time'])); // Format date and time

                    echo "<div class='card mt-3'>";
                    echo "    <div class='card-body'>";
                    echo "        <input type='hidden' class='event-id' value='$event_id' />"; // Hidden event ID for future use
                    echo "        <h5 class='card-title'>$title</h5>";
                    echo "        <p class='card-subtitle mb-2 text-muted'>$event_time</p>"; // Event time on its own line
                    echo "        <p class='card-subtitle mb-2 text-muted'>$location</p>"; // Location on its own line
                    echo "        <p class='card-text'>$description</p>";
                    echo "        <button type='button' class='btn btn-secondary' data-bs-toggle='modal' data-bs-target='#editModal-$event_id'>Edit</button>";
                    echo "        <button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#feedbackModal-$event_id'>View Feedback</button>";
                    echo "        <button type='button' class='btn btn-info' data-bs-toggle='modal' data-bs-target='#participantsModal-$event_id'>View Participants</button>";
                    echo "        <a href='#' class='btn btn-danger delete-event' data-id='$event_id'>Delete</a>";
                    echo "    </div>";
                    echo "</div>";

                // Edit Modal
                echo "
                <div class='modal fade' id='editModal-$event_id' tabindex='-1' aria-labelledby='editModalLabel-$event_id' aria-hidden='true'>
                    <div class='modal-dialog'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='modal-title' id='editModalLabel-$event_id'>Edit Event</h5>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                <form id='edit-form-$event_id'>
                                    <div class='mb-3'>
                                        <label for='edit-title-$event_id' class='form-label'>Title</label>
                                        <input type='text' class='form-control' id='edit-title-$event_id' value='$title' required>
                                    </div>
                                    <div class='mb-3'>
                                        <label for='edit-description-$event_id' class='form-label'>Description</label>
                                        <textarea class='form-control' id='edit-description-$event_id' required>$description</textarea>
                                    </div>
                                    <div class='mb-3'>
                                        <label for='edit-location-$event_id' class='form-label'>Location</label>
                                        <input type='text' class='form-control' id='edit-location-$event_id' value='$location' required>
                                    </div>
                                    <div class='mb-3'>
                                        <label for='edit-event-date-$event_id' class='form-label'>Event Date</label>
                                        <input type='datetime-local' class='form-control' id='edit-event-date-$event_id' value='" . date("Y-m-d\TH:i", strtotime($event_time)) . "' required>
                                    </div>
                                </form>
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                                <button type='button' class='btn btn-primary' onclick='submitEdit($event_id)'>Save Changes</button>
                            </div>
                        </div>
                    </div>
                </div>
                ";

                // Feedback Modal
                echo "
                <div class='modal fade' id='feedbackModal-$event_id' tabindex='-1' aria-labelledby='feedbackModalLabel-$event_id' aria-hidden='true'>
                    <div class='modal-dialog modal-lg'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='modal-title' id='feedbackModalLabel-$event_id'>Feedback for $title</h5>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                <div id='feedback-content-$event_id' style='max-height: 400px; overflow-y: auto;'>
                                    Loading feedback...
                                </div>
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                ";

                // Participants Modal
                echo "
                <div class='modal fade' id='participantsModal-$event_id' tabindex='-1' aria-labelledby='participantsModalLabel-$event_id' aria-hidden='true'>
                    <div class='modal-dialog modal-lg'>
                        <div class='modal-content'>
                            <div class='modal-header'>
                                <h5 class='modal-title' id='participantsModalLabel-$event_id'>Participants for $title</h5>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>
                                <p id='participant-count-$event_id'>Loading participant count...</p>
                                <ul id='participant-list-$event_id' style='max-height: 400px; overflow-y: auto;'></ul>
                            </div>
                            <div class='modal-footer'>
                                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                ";
            }
} else {
    echo "<p>No events found.</p>";
}

            $stmt->close();
        ?>


        <!-- Edit Event Layout (Template for Edit)
        <h2>Edit Event</h2>
        <form action="admin_home.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="Event ID here">
            <div class="form-group">
                <label for="edit_title">Event Title</label>
                <input type="text" class="form-control" id="edit_title" name="title" value="Current event title here" required>
            </div>
            <div class="form-group">
                <label for="edit_description">Event Description</label>
                <textarea class="form-control" id="edit_description" name="description" required>Current event description here</textarea>
            </div>
            <div class="form-group">
                <label for="edit_event_date">Event Date</label>
                <input type="datetime-local" class="form-control" id="edit_event_date" name="event_date" value="Current event date here" required>
            </div>
            <div class="form-group">
                <label for="edit_location">Location</label>
                <input type="text" class="form-control" id="edit_location" name="location" value="Current location here" required>
            </div>
            <button type="submit" class="btn btn-warning mt-2">Update Event</button>
        </form> -->

    </div>

</main>

<?php
// End the HTML layout
endHTML();
?>