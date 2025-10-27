<?php
session_start();
include 'connect.php';
include 'header.php';
include 'sidebar.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Edit mode
$edit_mode = false;
$event = [
    "event_name" => "",
    "description" => "",
    "date" => "",
    "location" => "",
    "event_type" => "",
    "image" => ""
];

// Flash message
$flash_msg = '';

if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql = "SELECT * FROM event_table WHERE event_id = $edit_id AND created_by = $user_id LIMIT 1";
    $result = mysqli_query($con, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $event = mysqli_fetch_assoc($result);
        $edit_mode = true;
    } else {
        die("❌ You are not allowed to edit this event.");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($con, $_POST['event_name']);
    $desc = mysqli_real_escape_string($con, $_POST['description']);
    $date = $_POST['date'];
    $location = mysqli_real_escape_string($con, $_POST['location']);
    $type = mysqli_real_escape_string($con, $_POST['event_type']);

    $image_name = $event['image'];
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image_name);
    }

    $success = false;
    if ($edit_mode) {
        $update_sql = "UPDATE event_table SET 
                        event_name='$name', 
                        description='$desc', 
                        date='$date', 
                        location='$location', 
                        event_type='$type', 
                        image='$image_name' 
                      WHERE event_id=$edit_id AND created_by=$user_id";
        if (mysqli_query($con, $update_sql)) $success = true;
        $event_id = $edit_id;
    } else {
        $insert_sql = "INSERT INTO event_table (event_name, description, `date`, location, event_type, image, created_by) 
                       VALUES ('$name','$desc','$date','$location','$type','$image_name',$user_id)";
        if (mysqli_query($con, $insert_sql)) {
            $success = true;
            $event_id = mysqli_insert_id($con);
        } else {
            $flash_msg = "❌ Error: " . mysqli_error($con);
        }
    }

    if ($success && empty($flash_msg)) {
        $flash_msg = $edit_mode ? "✅ Event updated successfully!" : "✅ Event created successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= $edit_mode ? "Edit Event" : "Create Event" ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f0f2f5; font-family: 'Poppins', sans-serif; margin-left: 240px; padding-top: 70px; }
.form-container { max-width: 700px; margin: 40px auto; background: #fff; padding: 30px 25px; border-radius: 12px; box-shadow: 0 6px 20px rgba(0,0,0,0.15); }
.form-container h2 { color: #6f42c1; font-weight: 700; margin-bottom: 25px; }
.form-label { font-weight: 500; color: #333; }
.form-control { border-radius: 8px; border: 1px solid #ccc; }
.btn-success { background: #6f42c1; border: none; font-weight: 600; }
.btn-success:hover { background: #5936a6; transform: translateY(-2px); }
.alert { font-weight:600; text-align:center; border-radius:10px; margin-bottom:20px; }
.alert-success { background:#dcfce7; color:#166534; border:1px solid #86efac; }
.alert-danger { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
</style>
</head>
<body>
<div class="form-container">
    <h2 class="text-center mb-4"><?= $edit_mode ? "Edit Event" : "Create Event" ?></h2>

    <?php if ($flash_msg): ?>
        <div class="alert <?= strpos($flash_msg,'✅')!==false?'alert-success':'alert-danger' ?>">
            <?= $flash_msg ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Event Name</label>
            <input type="text" name="event_name" class="form-control" required value="<?= htmlspecialchars($event['event_name']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($event['description']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="date" class="form-control" required value="<?= htmlspecialchars($event['date']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" required value="<?= htmlspecialchars($event['location']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Event Type</label>
            <select name="event_type" class="form-control" required>
                <option value="Free" <?= ($event['event_type']=='Free')?'selected':'' ?>>Free</option>
                <option value="Paid" <?= ($event['event_type']=='Paid')?'selected':'' ?>>Paid</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Event Image</label><br>
            <?php if(!empty($event['image'])): ?>
                <img src="uploads/<?= htmlspecialchars($event['image']) ?>" width="120" class="mb-2"><br>
            <?php endif; ?>
            <input type="file" name="image" class="form-control">
        </div>

        <button type="submit" class="btn btn-success w-100"><?= $edit_mode ? "Update Event" : "Create Event" ?></button>
    </form>
</div>
</body>
</html>
