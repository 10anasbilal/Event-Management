<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = intval($_SESSION['user_id']);

if (!isset($_GET['event_id']) || !is_numeric($_GET['event_id'])) {
    die("Invalid request!");
}
$event_id = intval($_GET['event_id']);

// (favourite toggle logic here — unchanged)
$check_sql = "SELECT id FROM favt_table WHERE user_id = ? AND event_id = ?";
$stmt = $con->prepare($check_sql);
$stmt->bind_param("ii", $user_id, $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $delete_sql = "DELETE FROM favt_table WHERE user_id = ? AND event_id = ?";
    $stmt_del = $con->prepare($delete_sql);
    $stmt_del->bind_param("ii", $user_id, $event_id);
    $stmt_del->execute();
} else {
    $insert_sql = "INSERT INTO favt_table (user_id, event_id, created_at) VALUES (?, ?, NOW())";
    $stmt_ins = $con->prepare($insert_sql);
    $stmt_ins->bind_param("ii", $user_id, $event_id);
    $stmt_ins->execute();
}

// Build redirect: prefer HTTP_REFERER, otherwise upcoming_events.php
$ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'upcoming_events.php';

// Remove any existing fragment from ref
$parts = parse_url($ref);
$base = '';
if (isset($parts['scheme'])) {
    // absolute URL
    $base = $parts['scheme'] . '://' . $parts['host'];
    if (isset($parts['port'])) $base .= ':' . $parts['port'];
    if (isset($parts['path'])) $base .= $parts['path'];
    if (isset($parts['query'])) $base .= '?' . $parts['query'];
} else {
    // relative URL
    $base = $parts['path'] ?? 'upcoming_events.php';
    if (isset($parts['query'])) $base .= '?' . $parts['query'];
}

// Append fragment to jump to event
$redirect = $base . '#event-' . $event_id;

header("Location: $redirect");
exit();
