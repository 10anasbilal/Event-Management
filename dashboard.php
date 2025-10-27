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
$role = $_SESSION['role'] ?? 'user'; // admin or user

$today = date('Y-m-d');

// Stats queries
$where = "1"; 
$total_events = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM event_table WHERE $where"))['total'];
$upcoming = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM event_table WHERE $where AND date >= '$today'"))['total'];
$past = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM event_table WHERE $where AND date < '$today'"))['total'];

$total_users = 0;
if ($role === 'admin') {
    $total_users = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS total FROM user_table"))['total'];
}

// Latest 5 events
$latest_events = mysqli_query($con, "SELECT event_name, date, location, event_type FROM event_table WHERE $where ORDER BY date DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background: #f5f5f5;
    overflow-x: hidden; /* Horizontal scroll hatao */
}

/* Sidebar-aware container */
.container {
    margin-left: 260px; /* Sidebar width */
    margin-top: 80px;   /* Header height */
    padding: 20px 30px;
    width: calc(100% - 260px);
    overflow-x: hidden;
}

/* Stats Cards */
.dashboard-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 25px;
}

.stat-card {
    flex: 1 1 200px;
    text-decoration: none;
}
.stat-card .card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s, background 0.2s, color 0.2s;
}
.stat-card:hover .card {
    transform: translateY(-5px);
    background: #6f42c1;
    color: #fff;
}
.stat-card .card h2 {
    font-size: 28px;
    font-weight: 700;
}
.stat-card .card h4 {
    font-weight: 500;
}

/* Latest Events Table */
.card.table-card {
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    background: #fff;
    overflow-x: auto; /* Mobile me scroll allow */
}

.table thead {
    background: #6f42c1;
    color: #fff;
}
.table tbody tr:hover {
    background: #f0e6ff;
}
.table td, .table th {
    vertical-align: middle;
    white-space: nowrap;
}

/* Quick Actions (optional) */
.quick-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 25px;
}
.quick-actions a {
    flex: 1 1 150px;
    background: #6f42c1;
    color: #fff;
    text-align: center;
    padding: 15px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.2s, transform 0.2s;
}
.quick-actions a:hover {
    background: #5936a6;
    transform: translateY(-3px);
}

/* Responsive */
@media (max-width: 768px) {
    .container {
        margin-left: 60px; /* collapsed sidebar */
        width: calc(100% - 60px);
        padding: 15px;
    }
    .dashboard-row, .quick-actions {
        flex-direction: column;
    }
    .table-card {
        padding: 10px;
    }
}
</style>
</head>
<body>

<div class="container">

    <h2 class="mb-4">Welcome, <?= ucfirst($role) ?>!</h2>

    <!-- Stats Cards -->
    <div class="dashboard-row">
        <?php if($role === 'admin'): ?>
        <a href="display.php" class="stat-card">
            <div class="card p-3">
                <h4>Total Users</h4>
                <h2><?= $total_users ?></h2>
            </div>
        </a>
        <?php endif; ?>

        <a class="stat-card">
            <div class="card p-3">
                <h4>Total Events</h4>
                <h2><?= $total_events ?></h2>
            </div>
        </a>

        <a href="upcoming_events.php" class="stat-card">
            <div class="card p-3">
                <h4>Upcoming Events</h4>
                <h2><?= $upcoming ?></h2>
            </div>
        </a>

        <a class="stat-card">
            <div class="card p-3">
                <h4>Past Events</h4>
                <h2><?= $past ?></h2>
            </div>
        </a>
    </div>

    <!-- Latest Events Table -->
    <div class="card table-card">
        <h4 class="mb-3">Latest 5 Events</h4>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($latest_events) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($latest_events)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td><?= htmlspecialchars(ucfirst($row['event_type'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center text-muted">No events found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
