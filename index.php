<?php

    // Include Configuaration File.
    require_once($_SERVER['DOCUMENT_ROOT']."/includes/config.php");

    // Initialize the Database.
    $db = initializeDB();

    // Load Events
    $upcomingEvents = array();
    $sql = "SELECT * FROM event WHERE event_time > NOW() ORDER BY event_time ASC";
    $result=mysqli_query($db, $sql);
    while($a_row = mysqli_fetch_assoc($result)) {
        $upcomingEvents[$a_row['id']] = $a_row;   // Event Listings
    }

    startHTML("Home");  //Edit "Login" to be the Corresponding Page Title.

    includeHeader(); 

    // If the user is logged in, fetch RSVP'd events
$rsvpdEvents = [];
if (!empty($_SESSION['user_id'])) {
    $sql = "
        SELECT 
            e.id, 
            e.title, 
            e.description, 
            e.location, 
            e.event_time 
        FROM 
            event_user_map eum
        JOIN 
            event e ON eum.event_id = e.id
        WHERE 
            eum.user_id = ?
        ORDER BY 
            e.event_time ASC";

    $stmt = $db->prepare($sql);
    $user_id = $_SESSION['user_id'];
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $rsvpdEvents[$row['id']] = $row; // RSVP'd Event Listings
    }

    $stmt->close();
}

?>

<!-- HTML Goes Here. -->

<?php if (!isset($_SESSION['user_id'])): ?>
<div class="container">
	<div class="px-4 py-5 my-5 text-center">
		<h1 class="display-5 fw-bold text-body-emphasis">Welcome to Campus Life!</h1>
		<div class="col mx-auto">
		  <p class="lead mb-4 mx-auto">Your one-stop hub for all things campus life at UNR! Discover a diverse lineup of upcoming events and opportunities to connect with fellow students. Sign in or create an account to browse, RSVP for events, and get ready to run with the Pack. After attending, share your feedback to help shape future events and make your voice heard in the UNR community. Join us, and let’s make every event an experience worth remembering!</p>
		  <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
			<button type="button" class="btn btn-primary btn-lg px-4 gap-3" data-bs-toggle="modal" data-bs-target="#modalSignin">Login</button>
			<a class="btn btn-outline-secondary btn-lg px-4 gap-3" href="signup.php" role="button">Sign Up</a>
		  </div>
		</div>
	</div>
</div>
<?php endif; ?>

<div class="container">
  <div class="row g-5">
	
	<!-- Sidebar -->
    <div class="col-md-4 col-lg-3 p-4 border-end">
      <h2 class="">My Events</h2>
      </br>
      <?php if (empty($_SESSION['user_id'])): ?>
        <p>Log in to view your events.</p>
      <?php else: ?>
        <!-- Display RSVP'd Events -->
        <?php if (!empty($rsvpdEvents)): ?>
          <div class="row row-cols-1">
            <?php foreach ($rsvpdEvents as $event): ?>
              <div class="col">
                <div class="card mb-2">
                  <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                    <h6 class="card-subtitle mb-2 text-body-secondary">
                        <?php echo date('F j, Y, g:i A', strtotime($event['event_time'])); ?>S
                    </h6>
                    <p class="card-text text-muted"><?php echo htmlspecialchars($event['location']); ?></p>
                    <p class="card-text"><?php echo htmlspecialchars($event['description']); ?></p>

                    <!-- Un-RSVP Button -->
                    <button type="button" class="btn btn-danger remove-btn" data-event-id="<?php echo $event['id']; ?>">Remove</button></br>
                    <!-- Delete Modal -->
                    <div class="modal fade" id="Del-Modal" tabindex="-1" aria-labelledby="modal" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <span class="h1 modal-title fs-5" id="modal">Delete Event</span>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to remove this event?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-danger">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Feedback Button -->
                    <button type="button" class="btn btn-link mt-2" data-bs-toggle="modal" data-bs-target="#Feedback-modal-<?php echo $event['id']; ?>">Leave Feedback</button>
                    <!-- Feedback Modal -->
                    <div class="modal fade" id="Feedback-modal-<?php echo $event['id']; ?>" tabindex="-1" aria-labelledby="modal" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <span class="h1 modal-title fs-5" id="modal">Leave Feedback</span>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Feedback Form -->
                                    <form id="feedback-form-<?php echo $event['id']; ?>">
                                        <div class="mb-3">
                                            <label for="feedback-text-<?php echo $event['id']; ?>" class="form-label">Your Feedback</label>
                                            <textarea class="form-control" id="feedback-text-<?php echo $event['id']; ?>" rows="3" maxlength="255" required></textarea>
                                            <div class="form-text">Maximum 255 characters.</div>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="anonymous-<?php echo $event['id']; ?>">
                                            <label class="form-check-label" for="anonymous-<?php echo $event['id']; ?>">
                                                Send Feedback Anonymously
                                            </label>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" onclick="submitFeedback(<?php echo $event['id']; ?>)">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p>You have not RSVP'd to any events.</p>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <!-- Main Content -->
    <div class="col-md-8 col-lg-9 p-4">
      <h2 class="">All Events</h2>
	  </br>
      <?php if (empty($upcomingEvents)): ?>
        <p>Sorry, there are currently no upcoming events. Please check back later!</p>
      <?php else: ?>
        <!-- Display Upcoming Events -->
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
          <?php foreach ($upcomingEvents as $event): ?>
            <div class="col">
              <div class="card h-100">
                <div class="card-body btn-bottom">
                  <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                  <h6 class="card-subtitle mb-2 text-body-secondary">
                    <?php echo date('F j, Y, g:i A', strtotime($event['event_time'])); ?>
                  </h6>
                  <p class="card-text text-muted"><?php echo htmlspecialchars($event['location']); ?></p>
                  <p class="card-text"><?php echo htmlspecialchars($event['description']); ?></p>

                  <!-- RSVP Button -->
                  <button type="button" class="btn btn-primary rsvp-btn" data-event-id="<?php echo $event['id']; ?>">RSVP</button>

                  <!-- RSVP Modal -->
                  <div class="modal fade" id="modal-<?php echo $event['id']; ?>" tabindex="-1" aria-labelledby="modalLabel-<?php echo $event['id']; ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header">
                          <span class="h1 modal-title fs-5" id="modalLabel-<?php echo $event['id']; ?>">Confirmation</span>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <p>Are you sure you want to RSVP for "<?php echo htmlspecialchars($event['title']); ?>"?</p>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <!-- Confirm RSVP Button -->
                          <button type="button" class="btn btn-primary confirm-rsvp" data-event-id="<?php echo $event['id']; ?>">Confirm RSVP</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
mysqli_close($db);
endHTML();
?>
