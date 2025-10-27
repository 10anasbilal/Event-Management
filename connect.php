<?php
$host = "localhost"; 
$user = "root"; 
$pass = ""; 
$dbname = "event_db"; // yahan apna database ka naam do


$con = new mysqli($host, $user, $pass, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
} else {
    echo "";
}
?>
