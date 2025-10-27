<?php
session_start();
include 'connect.php';
include 'header.php';
include 'sidebar.php';

// ✅ User logged in check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// ✅ Fetch only events created by this user
$events = mysqli_query($con, "SELECT event_id, event_name FROM event_table WHERE created_by=$user_id ORDER BY event_id DESC");

// ✅ Form submit
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = intval($_POST['event_id']);

    // ✅ Check if user owns this event
    $check_event = mysqli_query($con, "SELECT * FROM event_table WHERE event_id=$event_id AND created_by=$user_id");
    if (mysqli_num_rows($check_event) == 0) {
        $message = "<div class='alert alert-danger mt-3'>❌ You can only add categories to your own events!</div>";
    } else {
        $categories = $_POST['category_name'] ?? [];
        $prices = $_POST['price'] ?? [];
        $total_seats = $_POST['total_seats'] ?? [];

        $inserted = 0;
        foreach ($categories as $index => $cat_name) {
            $cat_name = trim($cat_name);
            $price = floatval($prices[$index]);
            $seats = intval($total_seats[$index]);

            if ($cat_name && $price && $seats) {
                $sql = "INSERT INTO seat_categories (event_id, category_name, price, total_seats, created_at)
                        VALUES ('$event_id', '$cat_name', '$price', '$seats', NOW())";
                if (mysqli_query($con, $sql)) {
                    $inserted++;
                }
            }
        }

        if ($inserted > 0) {
            $message = "<div class='alert alert-success mt-3'>✅ $inserted Categories added successfully!</div>";
        } else {
            $message = "<div class='alert alert-warning mt-3'>⚠️ Please fill all fields correctly.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Seat Category</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
/* (Same CSS as before) */
body { background: #f3f4f6; font-family: "Poppins", sans-serif; margin:0; padding:0; }
.main-content { margin-left: 260px; margin-top: 80px; padding: 40px 30px; min-height: calc(100vh - 80px); background: linear-gradient(135deg, #e0f2fe, #ecfdf5); display:flex; justify-content:center; }
.content-wrapper { background: #ffffff; width: 100%; max-width: 700px; padding: 40px 35px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); transition:0.3s ease; }
.content-wrapper:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(0,0,0,0.12); }
h2 { text-align:center; font-weight:700; color:#1e3a8a; margin-bottom:30px; letter-spacing:0.5px; }
.form-label { font-weight:600; color:#1e293b; margin-bottom:6px; }
.form-control, .form-select { border-radius:10px; border:1px solid #cbd5e1; padding:10px 14px; transition:0.2s; }
.form-control:focus, .form-select:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.2); }
.btn-submit { background: linear-gradient(135deg, #2563eb, #1e40af); color:white; border:none; font-weight:600; padding:10px 25px; border-radius:12px; transition:0.3s; box-shadow:0 3px 10px rgba(37,99,235,0.3); }
.btnn-submit { background: linear-gradient(135deg, #1bc720ff, #055f14ff); color:white; border:none; font-weight:600; padding:10px 25px; border-radius:12px; transition:0.3s; box-shadow:0 3px 10px rgba(37,99,235,0.3); }
.btn-submit:hover { background: linear-gradient(135deg, #1e3a8a, #1d4ed8); box-shadow:0 4px 12px rgba(37,99,235,0.45); transform:translateY(-2px); }
.text-center { margin-top:20px; }
.alert { font-weight:600; text-align:center; border-radius:10px; margin-top:15px; }
.alert-success { background:#dcfce7; color:#166534; border:1px solid #86efac; }
.alert-danger { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
.alert-warning { background:#fef9c3; color:#854d0e; border:1px solid #fde68a; }
.category-group { border:1px solid #cbd5e1; padding:15px; border-radius:10px; margin-bottom:15px; position:relative; }
.remove-btn { position:absolute; top:10px; right:10px; background:#e53935; color:white; border:none; border-radius:50%; width:25px; height:25px; font-weight:bold; cursor:pointer; }
@media (max-width:768px){ .main-content{ margin-left:0; padding:20px; } .content-wrapper{ padding:25px; } }
</style>
</head>
<body>

<div class="main-content">
<div class="content-wrapper">
<h2>Add Seat Category</h2>

<?= $message ?>

<form method="POST" id="categoryForm">
    <!-- Event Dropdown -->
    <div class="mb-3">
        <label class="form-label">Select Your Event</label>
        <select name="event_id" class="form-select" required>
            <option value="">-- Choose Event --</option>
            <?php while ($row = mysqli_fetch_assoc($events)): ?>
                <option value="<?= $row['event_id'] ?>"><?= htmlspecialchars($row['event_name']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div id="categoriesWrapper">
        <div class="category-group">
            <button type="button" class="remove-btn" onclick="this.parentElement.remove();">×</button>
            <div class="mb-3">
                <label class="form-label">Category Name</label>
                <input type="text" name="category_name[]" class="form-control" placeholder="e.g. VVIP, VIP, Economy" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Price per Seat (Rs.)</label>
                <input type="number" name="price[]" class="form-control" step="0.01" placeholder="e.g. 1500" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Total Seats</label>
                <input type="number" name="total_seats[]" class="form-control" placeholder="e.g. 50" required>
            </div>
        </div>
    </div>

    <div class="mb-3 text-center">
        <button type="button" class="btn-submit" onclick="addCategory()">Add New Category</button>
    </div>

    <div class="text-center">
        <button type="submit" class="btnn-submit">Setup Complete</button>
    </div>
</form>
</div>
</div>

<script>
function addCategory() {
    const wrapper = document.getElementById('categoriesWrapper');
    const newGroup = document.createElement('div');
    newGroup.className = 'category-group';
    newGroup.innerHTML = `
        <button type="button" class="remove-btn" onclick="this.parentElement.remove();">×</button>
        <div class="mb-3">
            <label class="form-label">Category Name</label>
            <input type="text" name="category_name[]" class="form-control" placeholder="e.g. VVIP, VIP, Economy" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Price per Seat (Rs.)</label>
            <input type="number" name="price[]" class="form-control" step="0.01" placeholder="e.g. 1500" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Total Seats</label>
            <input type="number" name="total_seats[]" class="form-control" placeholder="e.g. 50" required>
        </div>
    `;
    wrapper.appendChild(newGroup);
}
</script>

</body>
</html>
