<?php
session_start();
include 'connect.php';

if(!isset($_SESSION['user_id'])){
    exit("not_logged_in");
}

$user_id = $_SESSION['user_id'];
$event_id = $_POST['event_id'] ?? null;

if(!$event_id){
    exit("invalid_event");
}

// Check if already in favourites
$check_sql = "SELECT id FROM favt_table WHERE user_id = ? AND event_id = ?";
$stmt = $con->prepare($check_sql);
$stmt->bind_param("ii", $user_id, $event_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    // Remove favourite
    $delete_sql = "DELETE FROM favt_table WHERE user_id = ? AND event_id = ?";
    $stmt2 = $con->prepare($delete_sql);
    $stmt2->bind_param("ii", $user_id, $event_id);
    $stmt2->execute();
    echo "removed";
} else {
    // Add favourite
    $insert_sql = "INSERT INTO favt_table (user_id, event_id) VALUES (?, ?)";
    $stmt3 = $con->prepare($insert_sql);
    $stmt3->bind_param("ii", $user_id, $event_id);
    $stmt3->execute();
    echo "added";
}
?>
