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

// Fetch only current user's events
$sql = "SELECT * FROM event_table WHERE created_by = $user_id ORDER BY `date` ASC";
$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Events</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: #f0f2f5;
    font-family: 'Poppins', sans-serif;
    margin-left: 240px; /* sidebar width */
    padding-top: 70px;  /* header height */
}

/* Form Container */
.form-container {
    max-width: 900px;
    margin: 40px auto;
    background: #fff;
    padding: 30px 25px;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    transition: transform 0.3s, box-shadow 0.3s;
}
.form-container:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

/* Cards */
.card {
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s, box-shadow 0.3s;
}
.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

/* Card Images */
.card img {
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    height: 180px;
    object-fit: cover;
}

/* Headings */
.form-container h2, .card h4 {
    color: #6f42c1; /* purple theme */
    font-weight: 700;
}

/* Inputs & Textarea */
.form-control {
    border-radius: 8px;
    border: 1px solid #ccc;
    transition: all 0.3s ease;
}
.form-control:focus {
    border-color: #6f42c1;
    box-shadow: 0 0 5px rgba(111,66,193,0.3);
}

/* Buttons */
.btn-primary, .btn-success {
    background: #6f42c1;
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
}
.btn-primary:hover, .btn-success:hover {
    background: #5936a6;
    transform: translateY(-2px);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    body {
        margin-left: 0;
        padding-top: 120px;
    }
    .form-container {
        margin: 20px;
        padding: 25px;
    }
}
</style>

</head>
<body>
<div class="form-container">
    <h2 class="text-center mb-4">My Events</h2>
    <div class="row">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($event = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <?php if(!empty($event['image'])): ?>
                            <img src="uploads/<?= htmlspecialchars($event['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($event['event_name']) ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($event['event_name']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
                            <p><b>Date:</b> <?= htmlspecialchars($event['date']) ?></p>
                            <p><b>Location:</b> <?= htmlspecialchars($event['location']) ?></p>
                            <p><b>Type:</b> <?= ucfirst($event['event_type']) ?></p>

                           <div class="d-flex gap-2">
                                <a href="event_create.php?edit_id=<?= $event['event_id'] ?>" class="btn btn-warning w-50">Edit</a>
                                <a href="del_event.php?event_id=<?= $event['event_id'] ?>" class="btn btn-danger w-50" onclick="return confirm('Press OK to Delete this Event');">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">You haven't created any events yet.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
