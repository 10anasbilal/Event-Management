<?php
include 'connect.php';

if(isset($_POST['username'])){
    $username = $_POST['username'];

    $sql = "SELECT * FROM user_table WHERE username='$username'";
    $result = mysqli_query($con, $sql);

    if(mysqli_num_rows($result) > 0){
        echo "taken"; // already exists
    } else {
        echo "available"; // free hai
    }
}
?>
