<?php
session_start();
include 'connect.php';
include 'header.php';
include 'sidebar.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?msg=Please login first");
    exit();
}

$user_id = $_SESSION['user_id'];

// Upcoming events fetch
$sql = "SELECT * FROM event_table WHERE `date` >= CURDATE() ORDER BY `date` ASC";
$events_result = mysqli_query($con, $sql);
if(!$events_result){
    die("SQL Error: ".mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Upcoming Events</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f0f2f5;
    margin: 0;
    padding: 0;
    overflow-x: hidden; /* horizontal scroll remove */
}
.main-panel {
    margin-left: 260px; /* sidebar width */
    padding: 100px 30px 30px 30px; /* top padding for header */
    min-height: 100vh;
}

/* Page Heading */
h3.text-center {
    margin-bottom: 30px;
    color: #6f42c1;
    font-weight: 700;
}

/* Cards Container (grid) */
.row.cards-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

/* Individual Card */
.card {
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    transition: transform 0.3s, box-shadow 0.3s;
    background: #fff;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

/* Card Header */
.card-header {
    background: #6f42c1;
    color: #fff;
    font-size: 20px;
    font-weight: 600;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    padding: 12px 15px;
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

/* Buttons */
.card-body .btn {
    margin-top: 10px;
    border-radius: 8px;
    font-weight: 600;
    background: #6f42c1;
    border: none;
    color: #fff;
    transition: all 0.3s ease;
}
.card-body .btn:hover {
    background: #5936a6;
    transform: translateY(-2px);
}

/* Heart Icon */
.heart {
    font-size: 22px;
    text-decoration: none;
    margin-left: 10px;
    cursor: pointer;
    transition: all 0.2s;
}
.heart.red { color: red; }
.heart.gray { color: gray; }

/* Responsive Adjustments */
@media (max-width: 1200px) {
    .row.cards-container {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 768px) {
    .main-panel {
        margin-left: 0;
        padding: 80px 15px 15px 15px;
    }
    .row.cards-container {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>

<div class="main-panel">
    <h3 class="text-center">Upcoming Events</h3>
    <div class="row cards-container">
        <?php if(mysqli_num_rows($events_result) > 0): ?>
            <?php while($event = mysqli_fetch_assoc($events_result)): ?>
                <?php
                    $event_id = $event['event_id'];
                    // Check if already favourite
                    $fav_sql = "SELECT id FROM favt_table WHERE user_id = ? AND event_id = ?";
                    $stmt = $con->prepare($fav_sql);
                    $stmt->bind_param("ii", $user_id, $event_id);
                    $stmt->execute();
                    $fav_result = $stmt->get_result();
                    $is_fav = $fav_result->num_rows > 0;
                ?>
                <div class="card">
                    <?php if(!empty($event['image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($event['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($event['event_name']) ?>">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/400x180?text=No+Image" class="card-img-top" alt="No Image Available">
                    <?php endif; ?>


                    <div class="card-body">
                        <h5 class="card-title d-flex justify-content-between align-items-center">
                            <?= htmlspecialchars($event['event_name']) ?>
                            <span class="heart <?= $is_fav ? 'red' : 'gray' ?>" data-event-id="<?= $event_id ?>">
                                <?= $is_fav ? '❤️' : '🤍' ?>
                            </span>
                        </h5>
                        <p class="card-text"><?= htmlspecialchars($event['description']) ?></p>
                        <p><b>Date:</b> <?= htmlspecialchars($event['date']) ?></p>
                        <p><b>Location:</b> <?= htmlspecialchars($event['location']) ?></p>
                        <p><b>Type:</b> <?= ucfirst($event['event_type']) ?></p>
                        <a href="book_seat.php?event_id=<?= $event_id ?>" class="btn btn-primary w-100">Book Seat</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">No upcoming events available.</p>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function(){
    $(".heart").click(function(){
        const heart = $(this);
        const event_id = heart.data("event-id");

        $.ajax({
            url: "toggle_favt_ajax.php",
            method: "POST",
            data: { event_id: event_id },
            success: function(response){
                if(response === "added"){
                    heart.removeClass("gray").addClass("red").text("❤️");
                } else if(response === "removed"){
                    heart.removeClass("red").addClass("gray").text("🤍");
                }
            },
            error: function(){
                alert("Error toggling favourite!");
            }
        });
    });
});
</script>

</body>
</html>
