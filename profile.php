<?php
session_start();
include 'connect.php';
include 'header.php';
include 'sidebar.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please login first");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql = "SELECT name, username, email, mobile, address, role FROM user_table WHERE user_id = $user_id LIMIT 1";
$result = mysqli_query($con, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    die("User not found in database!");
}
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background: #f8f9fa;
    overflow-x: hidden; /* horizontal scroll remove */
}

/* Main Panel - sidebar aware */
.main-panel {
    margin-left: 260px; /* sidebar width */
    padding: 100px 30px 30px 30px; /* top padding for header */
    min-height: 100vh;
    background: #f8f9fa;
    overflow-x: hidden;
}

/* Profile / Card */
.card {
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    background: #fff;
    transition: transform 0.2s, box-shadow 0.2s;
}
.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

/* Card Header */
.card-header {
    background: #6f42c1; 
    color: #fff;
    font-size: 22px;
    font-weight: 600;
    text-align: center;
    padding: 15px;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

/* Card Body */
.card-body {
    padding: 25px;
}
.card-body h5 {
    margin-top: 15px;
    font-weight: 600;
    color: #343a40;
}
.card-body p {
    font-size: 15px;
    margin-bottom: 8px;
    color: #495057;
}

/* Buttons inside card */
.btn-custom {
    margin-top: 15px;
    border-radius: 8px;
    font-weight: 600;
    background: #6f42c1; 
    border: none;
    color: #fff;
    width: 100%;
    transition: all 0.3s ease;
}
.btn-custom:hover {
    background: #5936a6;
    transform: translateY(-2px);
}

/* Alerts / Messages */
.alert {
    margin-top: 20px;
    border-radius: 8px;
    text-align: center;
}

/* Responsive Adjustments */
@media (max-width: 1200px) {
    .main-panel {
        padding: 90px 20px 20px 20px;
    }
}
@media (max-width: 768px) {
    .main-panel {
        margin-left: 0;
        padding: 80px 15px 15px 15px;
    }
    .card {
        margin: 0 10px;
    }
}
</style>
</head>
<body>

<div class="main-panel">
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="container">
        <!-- Profile Card -->
        <div class="card col-md-8 mx-auto mb-5">
          <div class="card-header">
             Welcome, <?= htmlspecialchars($user['name']) ?>
          </div>
          <div class="card-body">
            <p class="text-center">Role: <b><?= ucfirst($user['role']) ?></b></p>
            <hr>
            <h5><b>Your Profile:</b></h5>
            <p><b>Name:</b> <?= htmlspecialchars($user['name']) ?></p>
            <p><b>Username:</b> <?= htmlspecialchars($user['username']) ?></p>
            <p><b>Email:</b> <?= htmlspecialchars($user['email']) ?></p>
            <p><b>Mobile:</b> <?= htmlspecialchars($user['mobile']) ?></p>
            <p><b>Address:</b> <?= htmlspecialchars($user['address']) ?></p>
            <hr>
            <a href="edit_profile.php?id=<?= $user_id ?>" class="btn btn-warning btn-custom w-100">Edit Profile</a>
          </div>
        </div>
    </div>
</div>

</body>
</html>
