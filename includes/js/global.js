// Set the current year dynamically in the footer
document.addEventListener('DOMContentLoaded', function () {
    // Dynamically set the year in the footer
    const yearElement = document.getElementById('year');
    if (yearElement) {
        const currentYear = new Date().getFullYear();
        yearElement.textContent = currentYear;
    }

    const signInModal = document.getElementById('modalSignin');
    const keepOpen = signInModal.getAttribute('data-keep-open') === 'true';

    if (keepOpen) {
        const bootstrapModal = new bootstrap.Modal(signInModal);
        bootstrapModal.show();
    }

    // Initialize RSVP functionality
    initializeRsvpHandling();

    // Initialize Remove (Un-RSVP) functionality
    initializeRemoveEventHandling();

    // Initialize the delete event handling after the DOM has loaded
    initializeDeleteEventHandling();

    // Initialize the feedback modal functionality
    setupFeedbackModal();

    // Initialize the participants modal functionality
    setupParticipantsModal();

});

function initializeRsvpHandling() {
    const rsvpButtons = document.querySelectorAll('.rsvp-btn');

    rsvpButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const eventId = this.dataset.eventId;

            if (!window.isLoggedIn) {
                console.log("User is not logged in. Opening the sign-in modal...");
                const signInModal = new bootstrap.Modal(document.getElementById('modalSignin'));
                signInModal.show();
                return;
            }

            console.log("User is logged in. Opening the RSVP confirmation modal...");
            const rsvpModal = new bootstrap.Modal(document.getElementById(`modal-${eventId}`));
            rsvpModal.show();

            // Add event listener for the confirm RSVP button in the modal
            const confirmRsvpButton = document.querySelector(`#modal-${eventId} .confirm-rsvp`);
            confirmRsvpButton.addEventListener(
                'click',
                function () {
                    console.log("Confirm RSVP clicked for event ID:", eventId);

                    // Send RSVP request to the server
                    fetch('/admin/rsvp.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ event_id: eventId })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                console.log("RSVP successful:", data);
                                alert('RSVP successful!');
                                location.reload(); // Refresh the page to show updated RSVP'd events
                            } else {
                                console.error("RSVP failed:", data.error);
                                alert(`Failed to RSVP: ${data.error}`);
                            }
                        })
                        .catch(error => {
                            console.error("Error during RSVP request:", error);
                            alert("An error occurred while trying to RSVP. Please try again later.");
                        });

                    // Close the RSVP modal
                    rsvpModal.hide();
                },
                { once: true } // Ensure the click event listener is added only once
            );
        });
    });
}

function initializeRemoveEventHandling() {
    const removeButtons = document.querySelectorAll('.remove-btn');

    removeButtons.forEach(button => {
        button.addEventListener('click', function () {
            const eventId = this.dataset.eventId;

            if (confirm("Are you sure you want to remove this event?")) {
                // Send Un-RSVP request to the server
                fetch('/admin/unrsvp.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ event_id: eventId }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Event removed successfully!");
                            location.reload(); // Refresh the page to update the list
                        } else {
                            alert("Error: " + data.error);
                        }
                    })
                    .catch(error => {
                        console.error("Error during removal:", error);
                        alert("An error occurred. Please try again.");
                    });
            }
        });
    });
}

function submitFeedback(eventId) {
    const feedbackText = document.getElementById(`feedback-text-${eventId}`).value.trim();
    const anonymous = document.getElementById(`anonymous-${eventId}`).checked;

    if (!feedbackText) {
        alert("Please enter your feedback before submitting.");
        return;
    }

    // Prepare data for submission
    const data = {
        event_id: eventId,
        feedback: feedbackText,
        anonymous: anonymous
    };

    // Send the feedback to the server
    fetch('/admin/submit_feedback.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert("Thank you for your feedback!");
                location.reload(); // Refresh the page
            } else {
                alert("Failed to submit feedback: " + result.error);
            }
        })
        .catch(error => {
            console.error("Error submitting feedback:", error);
            alert("An error occurred while submitting your feedback. Please try again later.");
        });
}

function initializeDeleteEventHandling() {
    const deleteButtons = document.querySelectorAll('.delete-event');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const eventId = this.dataset.id;

            if (confirm("Are you sure you want to delete this event? This action cannot be undone.")) {
                fetch('/admin/delete_event.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ event_id: eventId }),
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Event deleted successfully!");
                            location.reload(); // Refresh the page to reflect changes
                        } else {
                            alert("Failed to delete event: " + data.error);
                        }
                    })
                    .catch(error => {
                        console.error("Error during deletion:", error);
                        alert("An error occurred while deleting the event. Please try again later.");
                    });
            }
        });
    });
}

function submitEdit(eventId) {
    const title = document.getElementById(`edit-title-${eventId}`).value.trim();
    const description = document.getElementById(`edit-description-${eventId}`).value.trim();
    const eventLocation = document.getElementById(`edit-location-${eventId}`).value.trim();
    const eventDate = document.getElementById(`edit-event-date-${eventId}`).value.trim();

    if (!title || !description || !location || !eventDate) {
        alert("All fields are required.");
        return;
    }

    fetch('/admin/edit_event.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            event_id: eventId,
            title: title,
            description: description,
            location: eventLocation,
            event_date: eventDate,
        }),
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert("Event updated successfully!");
                location.reload(); // Reload the page to show updated data
            } else {
                alert("Failed to update event: " + data.error);
            }
        })
        .catch(error => {
            console.error("Error updating event:", error);
            alert("An error occurred. Please try again later.");
        });
}

function setupFeedbackModal() {
    const feedbackButtons = document.querySelectorAll('[data-bs-target^="#feedbackModal-"]');

    feedbackButtons.forEach(button => {
        button.addEventListener('click', function () {
            const eventId = button.getAttribute('data-bs-target').replace('#feedbackModal-', '');
            const feedbackContent = document.getElementById(`feedback-content-${eventId}`);
            
            // Call the function to fetch and populate feedback
            fetchFeedback(eventId, feedbackContent);
        });
    });
}

function fetchFeedback(eventId, feedbackContent) {
    feedbackContent.innerHTML = "Loading feedback...";
    
    fetch(`/admin/fetch_feedback.php?event_id=${eventId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.feedback.length > 0) {
                feedbackContent.innerHTML = data.feedback
                    .map(fb => `
                        <div>
                            <p>${fb.feedback}</p>
                            <small class="text-muted">Submitted on: ${new Date(fb.created_at).toLocaleString()}</small>
                            <hr>
                        </div>
                    `)
                    .join('');
            } else {
                feedbackContent.innerHTML = "<p>No feedback available for this event.</p>";
            }
        })
        .catch(error => {
            console.error("Error fetching feedback:", error);
            feedbackContent.innerHTML = "<p>An error occurred while fetching feedback. Please try again later.</p>";
        });
}

function setupParticipantsModal() {
    const participantButtons = document.querySelectorAll('[data-bs-target^="#participantsModal-"]');

    participantButtons.forEach(button => {
        button.addEventListener('click', function () {
            const eventId = button.getAttribute('data-bs-target').replace('#participantsModal-', '');
            const participantCount = document.getElementById(`participant-count-${eventId}`);
            const participantList = document.getElementById(`participant-list-${eventId}`);
            
            // Fetch participants
            fetch(`/admin/fetch_participants.php?event_id=${eventId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const participants = data.participants;
                        participantCount.textContent = `${participants.length} participant(s) RSVP'd`;
                        participantList.innerHTML = participants
                            .map(p => `<li>${p.name} - ${p.email}</li>`)
                            .join('');
                    } else {
                        participantCount.textContent = "No participants found.";
                        participantList.innerHTML = "";
                    }
                })
                .catch(error => {
                    participantCount.textContent = "An error occurred while fetching participants.";
                    participantList.innerHTML = "";
                });
        });
    });
}