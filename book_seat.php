<?php
session_start();
include 'connect.php';
include 'header.php';
include 'sidebar.php';

// ✅ Step 1: Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// ✅ Step 2: Get event ID from URL
if (!isset($_GET['event_id'])) {
    die("Event not found.");
}
$event_id = intval($_GET['event_id']);

// ✅ Step 3: Fetch event details
$event_sql = "SELECT * FROM event_table WHERE event_id = $event_id";
$event_result = mysqli_query($con, $event_sql) or die(mysqli_error($con));
$event = mysqli_fetch_assoc($event_result);

if (!$event) {
    die("Invalid Event ID.");
}

// ✅ Step 4: Fetch seat categories for this event
$categories_sql = "SELECT * FROM seat_categories WHERE event_id = $event_id";
$categories_result = mysqli_query($con, $categories_sql) or die(mysqli_error($con));

// ✅ Step 5: Handle booking form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = intval($_POST['category_id']);
    $seats_booked = intval($_POST['seats_booked']);

    // Fetch category details
    $cat_sql = "SELECT * FROM seat_categories WHERE category_id = $category_id";
    $cat_result = mysqli_query($con, $cat_sql);
    $category = mysqli_fetch_assoc($cat_result);

    if (!$category) {
        echo "<script>alert('Invalid seat category selected!');</script>";
    } else {
        $available_seats = $category['total_seats'];
        $price_per_seat = $category['price'];
        $total_amount = $price_per_seat * $seats_booked;

        // ✅ Check availability
        if ($seats_booked > $available_seats) {
            echo "<script>alert('Only $available_seats seats are available in this category!');</script>";
        } else {
            // ✅ Insert booking
            $insert_sql = "INSERT INTO bookings 
                (user_id, event_id, category_id, seats_booked, price_per_seat, total_amount, booking_status, payment_status)
                VALUES ('$user_id', '$event_id', '$category_id', '$seats_booked', '$price_per_seat', '$total_amount', 'reserved', 'pending')";
            
            if (mysqli_query($con, $insert_sql)) {
                // ✅ Update remaining seats
                $new_total = $available_seats - $seats_booked;
                mysqli_query($con, "UPDATE seat_categories SET total_seats = $new_total WHERE category_id = $category_id");
                echo "<script>alert('Seat(s) booked successfully!'); window.location.href='my_bookings.php';</script>";
            } else {
                echo "<script>alert('Error while booking seat. Please try again.');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Seat - <?= htmlspecialchars($event['event_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #f0f4ff, #d9e4ff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }
        .card {
            border: none;
            border-radius: 18px;
            box-shadow: 0px 10px 30px rgba(0,0,0,0.15);
            padding: 25px;
            background-color: #fff;
        }
        .btn-book {
            background: linear-gradient(135deg, #10b981, #059669);
            border: none;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        .btn-book:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
        }
        .event-title {
            color: #1e3a8a;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .info-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 10px 15px;
            border: 1px solid #e5e7eb;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="card col-lg-6 mx-auto">
        <h2 class="text-center event-title mb-2">Book Your Seat</h2>
        <h5 class="text-center text-secondary mb-3"><?= htmlspecialchars($event['event_name']) ?></h5>
       
        <hr>

        <form method="POST">
            <!-- Seat Category -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Select Seat Category</label>
                <select name="category_id" class="form-select" required>
                    <option value="">-- Choose Category --</option>
                    <?php while ($row = mysqli_fetch_assoc($categories_result)): ?>
                        <option value="<?= $row['category_id'] ?>">
                            <?= htmlspecialchars($row['category_name']) ?> —
                            Rs.<?= number_format($row['price']) ?>/seat
                            (Available: <?= $row['total_seats'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Number of Seats -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Number of Seats</label>
                <input type="number" name="seats_booked" class="form-control" min="1" placeholder="Enter number of seats" required>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-book w-100 py-2">Confirm Booking</button>
        </form>
    </div>
</div>
</body>
</html>
