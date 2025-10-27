<?php
include 'connect.php';
include 'header.php';
include 'sidebar.php';

// ✅ Delete Category
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($con, "DELETE FROM seat_categories WHERE category_id = $id");
    echo "<script>alert('Category deleted successfully!');window.location='view_categories.php';</script>";
    exit;
}

// ✅ Update Category
if (isset($_POST['update_category'])) {
    $id = $_POST['category_id'];
    $name = $_POST['category_name'];
    $price = $_POST['price'];
    $seats = $_POST['total_seats'];

    $update = "UPDATE seat_categories SET category_name='$name', price='$price', total_seats='$seats' WHERE category_id='$id'";
    if (mysqli_query($con, $update)) {
        echo "<script>alert('Category updated successfully!');window.location='view_categories.php';</script>";
    } else {
        echo "<script>alert('Update failed!');</script>";
    }
}

// ✅ Fetch category for edit
$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = mysqli_query($con, "SELECT * FROM seat_categories WHERE category_id = $id");
    $editData = mysqli_fetch_assoc($res);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Seat Categories</title>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #f6f8fa;
    margin: 0;
    padding: 0;
}

/* ✅ Adjust page layout according to sidebar + header */
.main-content {
    margin-left: 240px; /* Sidebar width */
    margin-top: 80px;  /* Header height */
    padding: 30px;
}

/* ✅ Container box */
.container {
    padding: 30px;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    max-width: 1150px;
    margin: auto;
}

h2 {
    text-align: center;
    color: #1e40af;
    font-weight: 700;
    margin-bottom: 25px;
}

/* ✅ Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

th {
    background-color: #2563eb;
    color: white;
    text-transform: uppercase;
    font-size: 13px;
    letter-spacing: 0.5px;
}

tr:hover {
    background-color: #eef2ff;
}

/* ✅ Buttons */
.btn {
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 12px;
    transition: 0.3s;
}

.btn-edit {
    background-color: #4CAF50;
    color: white;
}

.btn-delete {
    background-color: #E53935;
    color: white;
}

.btn:hover {
    opacity: 0.85;
}

/* 🧾 Edit Form Styling */
.edit-form {
    background: #f1f5ff;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0px 3px 10px rgba(0,0,0,0.1);
}

.edit-form h3 {
    color: #1e3a8a;
    margin-bottom: 15px;
}

.edit-form label {
    font-weight: 600;
    display: block;
    margin-bottom: 5px;
    color: #334155;
}

.edit-form input {
    width: 100%;
    padding: 10px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    margin-bottom: 15px;
    font-size: 14px;
}

.btn-update {
    background: linear-gradient(135deg, #2563eb, #1e40af);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    transition: 0.3s;
}

.btn-update:hover {
    background: linear-gradient(135deg, #1e3a8a, #1d4ed8);
}

/* ✅ Responsive */
@media (max-width: 991px) {
    .main-content {
        margin-left: 0;
        margin-top: 100px;
        padding: 20px;
    }
}
</style>

<!-- ✅ Main Layout Wrapper -->
<div class="main-content">
    <div class="container">
        <h2>View All Seat Categories</h2>

        <?php if ($editData): ?>
            <div class="edit-form">
                <h3> Edit Category (ID: <?= $editData['category_id'] ?>)</h3>
                <form method="POST">
                    <input type="hidden" name="category_id" value="<?= $editData['category_id'] ?>">
                    
                    <label>Category Name:</label>
                    <input type="text" name="category_name" value="<?= $editData['category_name'] ?>" required>

                    <label>Price (Rs):</label>
                    <input type="number" name="price" value="<?= $editData['price'] ?>" required>

                    <label>Total Seats:</label>
                    <input type="number" name="total_seats" value="<?= $editData['total_seats'] ?>" required>

                    <button type="submit" name="update_category" class="btn-update">Update Category</button>
                </form>
            </div>
        <?php endif; ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Event Name</th>
                <th>Category</th>
                <th>Price (Rs)</th>
                <th>Total Seats</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>

            <?php
            $sql = "SELECT sc.category_id, e.event_name, sc.category_name, sc.price, sc.total_seats, sc.created_at 
                    FROM seat_categories sc
                    JOIN event_table e ON sc.event_id = e.event_id
                    ORDER BY sc.category_id DESC";

            $result = mysqli_query($con, $sql);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$row['category_id']}</td>
                        <td>{$row['event_name']}</td>
                        <td>{$row['category_name']}</td>
                        <td>{$row['price']}</td>
                        <td>{$row['total_seats']}</td>
                        <td>{$row['created_at']}</td>
                        <td>
                            <a class='btn btn-edit' href='view_categories.php?edit={$row['category_id']}'>Edit</a>
                            <a class='btn btn-delete' href='view_categories.php?delete={$row['category_id']}' onclick=\"return confirm('Delete this category?');\">Delete</a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center;'>No categories found.</td></tr>";
            }
            ?>
        </table>
    </div>
</div>
</body>
</html>