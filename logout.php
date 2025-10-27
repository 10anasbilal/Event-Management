<?php
session_start();
session_unset();
session_destroy();
header("Location: /event_project/login.php");
exit();
?>
