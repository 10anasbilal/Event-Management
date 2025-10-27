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

// ✅ Step 2: Fetch all bookings for this user
$sql = "
    SELECT b.*, e.event_name, e.date, e.location, s.category_name 
    FROM bookings b
    JOIN event_table e ON b.event_id = e.event_id
    JOIN seat_categories s ON b.category_id = s.category_id
    WHERE b.user_id = $user_id
    ORDER BY b.booking_id DESC
";
$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Bookings</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background: #f5f7fa; font-family: 'Poppins', sans-serif; }
.main-content { margin-left: 260px; padding: 100px 30px 30px; min-height: 100vh; background: linear-gradient(135deg, #e0f7fa, #f0fdf4); transition: all 0.3s ease-in-out; }
.content-wrapper { background: white; border-radius: 15px; padding: 25px 30px; box-shadow: 0px 6px 25px rgba(0,0,0,0.1); }
h2 { color: #1e40af; font-weight: 700; text-align: center; margin-bottom: 30px; }
.card { border: none; border-radius: 15px; box-shadow: 0px 8px 25px rgba(0, 0, 0, 0.1); transition: transform 0.2s ease-in-out; background: #ffffff; margin-bottom:20px; }
.card:hover { transform: translateY(-5px); }
.card-title { font-weight: 700; color: #1e40af; }
.status { font-weight: 600; padding: 6px 14px; border-radius: 12px; display: inline-block; }
.reserved { background-color: #e0f2fe; color: #0369a1; }
.confirmed { background-color: #dcfce7; color: #166534; }
.cancelled { background-color: #fee2e2; color: #991b1b; }
.btn-back { background: linear-gradient(135deg, #2563eb, #1e40af); color: white; border: none; border-radius: 8px; padding: 10px 22px; font-weight: 600; transition: 0.3s; text-decoration: none; }
.btn-back:hover { background: linear-gradient(135deg, #1e3a8a, #1d4ed8); color: #fff; }
.btn-download { background: #16a34a; color: white; border: none; border-radius: 8px; padding: 6px 14px; font-weight: 600; margin-top: 10px; cursor:pointer; }
.btn-download:hover { background: #15803d; }
.alert { background: #e0f2fe; color: #1e3a8a; border-radius: 12px; padding: 20px; font-weight: 500; text-align:center; }
@media (max-width: 991px){ .main-content{ margin-left:0; padding:100px 20px 20px; } }
</style>

<!-- html2pdf.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>

<body>

<div class="main-content">
<div class="content-wrapper">
<h2>My Bookings</h2>

<?php if (mysqli_num_rows($result) > 0): ?>
    <div class="row">
    <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="col-md-6 col-lg-4">
            <!-- Card wrapper with unique ID -->
            <div id="pdf-card-<?= $row['booking_id'] ?>" class="card p-3">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['event_name']) ?></h5>
                    <p class="mb-1"><strong>Date:</strong> <?= htmlspecialchars($row['date']) ?></p>
                    <p class="mb-1"><strong>Location:</strong> <?= htmlspecialchars($row['location']) ?></p>
                    <p class="mb-1"><strong>Category:</strong> <?= htmlspecialchars($row['category_name']) ?></p>
                    <p class="mb-1"><strong>Seats Booked:</strong> <?= $row['seats_booked'] ?></p>
                    <p class="mb-1"><strong>Total Amount:</strong> Rs.<?= number_format($row['total_amount']) ?></p>

                    <p class="mt-2">
                        <span class="status 
                            <?= ($row['booking_status'] == 'reserved') ? 'reserved' : (($row['booking_status'] == 'confirmed') ? 'confirmed' : 'cancelled'); ?>">
                            <?= ucfirst($row['booking_status']) ?>
                        </span>
                    </p>
                    <p><strong>Payment:</strong> <?= ucfirst($row['payment_status']) ?></p>

                    <!-- Download button -->
                    <button class="btn-download" onclick="downloadCard(<?= $row['booking_id'] ?>)">Download Ticket</button>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="alert">
        You haven’t booked any seats yet.
    </div>
<?php endif; ?>

<div class="text-center mt-4">
    <a href="upcoming_events.php" class="btn-back">← Back to Events</a>
</div>
</div>
</div>
<script>
function downloadCard(bookingId) {
    const element = document.getElementById('pdf-card-' + bookingId);
    const downloadBtn = element.querySelector('.btn-download');

    // 1️⃣ Hide download button temporarily
    downloadBtn.style.display = 'none';

    // 2️⃣ Options for html2pdf
    const opt = {
        margin:       0.5,               // thoda margin
        filename:     'Booking_' + bookingId + '.pdf',
        image:        { type: 'jpeg', quality: 1 },
        html2canvas:  { scale: 4, logging: true, useCORS: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    // 3️⃣ Generate PDF
    html2pdf().set(opt).from(element).save().then(() => {
        // 4️⃣ Show the button again
        downloadBtn.style.display = 'inline-block';
    });
}
</script>
</body>
</html>
