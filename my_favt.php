<?php
session_start();
include 'connect.php';
include 'header.php';
include 'sidebar.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Fetch favourite events for this user
$sql = "SELECT e.* FROM event_table e
        INNER JOIN favt_table f ON e.event_id = f.event_id
        WHERE f.user_id = ?
        ORDER BY e.`date` ASC";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$events_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Favourite Events</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* Main Panel aligned with sidebar */
body {
    font-family: 'Poppins', sans-serif;
    background: #f0f2f5;
    margin: 0;
    padding: 0;
}

.main-panel {
    margin-left: 240px; /* Sidebar width */
    padding: 100px 30px 30px 30px; /* Top padding for navbar */
    min-height: 100vh;
}

/* Page Heading */
h3.text-center {
    margin-bottom: 30px;
    color: #6f42c1;
    font-weight: 700;
    font-size: 28px;
}

/* Cards Container */
.cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    min-height: 300px; /* ensure enough height for empty message */
    align-items: start; /* cards start from top */
    justify-items: center; /* center horizontally */
}

/* Individual Card */
.card {
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    background: #fff;
    transition: transform 0.3s, box-shadow 0.3s;
    width: 100%;
    max-width: 300px; /* maintain uniform size */
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

/* Card Image */
.card img {
    height: 180px;
    object-fit: cover;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

/* Card Body */
.card-body {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 15px;
}

/* Card Title + Heart */
.card-title {
    font-size: 18px;
    font-weight: 600;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Buttons */
.card-body .btn {
    margin-top: 10px;
    border-radius: 8px;
    font-weight: 600;
    background: #6f42c1; /* Purple theme */
    border: none;
    color: #fff;
    width: 100%;
    transition: all 0.3s ease;
}
.card-body .btn:hover {
    background: #5936a6;
    transform: translateY(-2px);
}

/* Heart Icon */
.heart {
    font-size: 22px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}
.heart.red { color: red; }
.heart.gray { color: gray; }

/* Empty message centered in container */
.empty-message {
    grid-column: 1/-1; /* span entire grid */
    color: #6c757d;
    font-size: 20px;
    font-weight: 500;
    text-align: center;
    margin-top: 50px;
}

/* Responsive */
@media (max-width: 1200px) {
    .cards-container {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    }
}
@media (max-width: 768px) {
    .main-panel {
        margin-left: 0;
        padding: 80px 15px 15px 15px; /* mobile navbar top padding */
    }
    .cards-container {
        grid-template-columns: 1fr;
    }
}

</style>
</head>
<body>
<div class="main-panel">
    <h3 class="text-center mb-4">My Favourite Events</h3>
    <div class="cards-container">
    <?php if($events_result->num_rows > 0): ?>
        <?php while($event = $events_result->fetch_assoc()): ?>
            <?php $event_id = $event['event_id']; ?>
            <div class="card">
                <?php if(!empty($event['image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($event['image']) ?>" alt="<?= htmlspecialchars($event['event_name']) ?>">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title">
                        <?= htmlspecialchars($event['event_name']) ?>
                        <a href="toggle_favt.php?event_id=<?= $event_id ?>" class="heart red">❤️</a>
                    </h5>
                    <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
                    <p><b>Date:</b> <?= htmlspecialchars($event['date']) ?></p>
                    <p><b>Location:</b> <?= htmlspecialchars($event['location']) ?></p>
                    <p><b>Type:</b> <?= ucfirst($event['event_type']) ?></p>
                </div>
            </div>
        <?php endwhile; ?>
        <?php else: ?>
        <p class="empty-message">You have not added any events to favourites yet.</p>
        <?php endif; ?>

    </div>
</div>
</body>
</html>
