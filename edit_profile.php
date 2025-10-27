<?php
session_start();
include 'connect.php';

// agar user login nahi hai, to login page bhejo
if (!isset($_SESSION['user_id'])) {
    header("Location: /event_project/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// form submit hone par
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name    = mysqli_real_escape_string($con, $_POST['name']);
    $email   = mysqli_real_escape_string($con, $_POST['email']);
    $mobile  = mysqli_real_escape_string($con, $_POST['mobile']);
    $address = mysqli_real_escape_string($con, $_POST['address']);

    $sql = "UPDATE user_table 
            SET name='$name', email='$email', mobile='$mobile', address='$address' 
            WHERE user_id=$user_id";

    if (mysqli_query($con, $sql)) {
        header("Location: dashboard.php");
        exit();
    } else {
        die("❌ Error updating profile: " . mysqli_error($con));
    }
}

// ab include header/sidebar
include 'header.php';
include 'sidebar.php';

// current user info fetch
$result = mysqli_query($con, "SELECT name, username, email, mobile, address FROM user_table WHERE user_id=$user_id LIMIT 1");
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* Wrapper for header & sidebar compatibility */
.content-wrapper {
    margin-left: 250px; /* sidebar width */
    padding: 100px 20px 20px 20px; /* top padding for header */
    min-height: calc(100vh - 100px);
    background: #f8f9fa;
}

/* Card Styling */
.card {
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    max-width: 600px;
    margin: auto;
    transform: translateX(-50px); /* start thoda left se */
    opacity: 0;
    animation: slideRight 0.6s forwards;
}

@keyframes slideRight {
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.card-header {
    background: #081b2dff;
    color: white;
    font-size: 20px;
    font-weight: 600;
    text-align: center;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

textarea.form-control { resize: none; }

/* Responsive for smaller screens */
@media (max-width: 768px) {
    .content-wrapper { margin-left: 0; padding: 120px 15px 15px 15px; }
}
</style>
</head>
<body>

<div class="content-wrapper">
    <div class="card">
        <div class="card-header">Edit Your Profile</div>
        <div class="card-body">

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username (cannot edit)</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mobile</label>
                    <input type="text" name="mobile" class="form-control" value="<?= htmlspecialchars($user['mobile']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2" required><?= htmlspecialchars($user['address']) ?></textarea>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
            </form>

        </div>
    </div>
</div>

</body>
</html>
