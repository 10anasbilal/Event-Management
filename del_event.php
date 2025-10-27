<?php
session_start();
include 'connect.php';

// ✅ Step 1: Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ✅ Step 2: Check if event_id is passed
if (!isset($_GET['event_id'])) {
    die("❌ Event ID missing.");
}

$event_id = intval($_GET['event_id']);

// ✅ Step 3: Make sure this event belongs to the current user
$sql_check = "SELECT * FROM event_table WHERE event_id=$event_id AND created_by=$user_id LIMIT 1";
$result = mysqli_query($con, $sql_check);

if(mysqli_num_rows($result) === 0) {
    die("❌ You are not allowed to delete this event.");
}

// ✅ Step 4: Delete related categories first (if any)
mysqli_query($con, "DELETE FROM seat_categories WHERE event_id=$event_id");

// ✅ Step 5: Delete the event
if(mysqli_query($con, "DELETE FROM event_table WHERE event_id=$event_id")) {
    // ✅ Step 6: Redirect back to My Events page with success message
    header("Location: my_events.php?msg=deleted");
    exit();
} else {
    die("❌ Error deleting event: " . mysqli_error($con));
}
?>
