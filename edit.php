<?php
session_start();
include 'connect.php';
include 'header.php';
include 'sidebar.php';
// Check ID
if (!isset($_GET['id'])) {
    header("Location: display.php");
    exit();
}

$id = intval($_GET['id']);

// Fetch user data
$stmt = $con->prepare("SELECT name, username, email, mobile, address FROM user_table WHERE user_id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    die("❌ User not found!");
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = $_POST['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email    = $_POST['email'] ?? '';
    $mobile   = $_POST['mobile'] ?? '';
    $address  = $_POST['address'] ?? '';

    $stmt = $con->prepare("UPDATE user_table SET name=?, username=?, email=?, mobile=?, address=? WHERE user_id=?");
    $stmt->bind_param("sssssi", $name, $username, $email, $mobile, $address, $id);

    if ($stmt->execute()) {
        $stmt->close();
        echo "<script>window.location.href='display.php';</script>";
        exit();
    } else {
        die("❌ Error updating user: " . $con->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit User</title>
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
    transform: translateY(-50px);
    opacity: 0;
    animation: slideDown 1s forwards;
}
@keyframes slideDown {
    to { transform: translateY(0); opacity: 1; }
}

.card-header {
    background: #6f42c1;
    color: white;
    font-size: 22px;
    font-weight: 600;
    text-align: center;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

.card-body { padding: 20px; }
textarea.form-control { resize: none; }
.btn-custom { margin-top: 15px; }

/* Responsive for smaller screens */
@media (max-width: 768px) {
    .content-wrapper { margin-left: 0; padding: 120px 15px 15px 15px; }
}
</style>
</head>
<body>

<div class="content-wrapper">
    <div class="card">
        <div class="card-header">Edit User</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($row['name'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($row['username'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mobile</label>
                    <input type="text" name="mobile" class="form-control" value="<?= htmlspecialchars($row['mobile'] ?? '') ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($row['address'] ?? '') ?></textarea>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-warning btn-custom">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
