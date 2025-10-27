<?php
include 'connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // safe integer

    // Sahi column ka naam use karo (user_id)
    $deleteSql = "DELETE FROM user_table WHERE user_id = $id";

    if (mysqli_query($con, $deleteSql)) {
        echo "<script>alert('❌ User deleted successfully'); window.location='display.php';</script>";
        exit();
    } else {
        echo "Error deleting user: " . mysqli_error($con);
    }
} else {
    header("Location: display.php");
    exit();
}
?>
