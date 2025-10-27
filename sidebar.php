<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include 'connect.php';

$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? 'user';
$current_page = basename($_SERVER['PHP_SELF']); // active page detection
?>

<style>
/* Sidebar container */
.sidebar {
    position: fixed;
    top: 60px; /* header ke niche */
    left: 0;
    width: 230px;
    height: calc(100% - 60px);
    background: linear-gradient(135deg, #ffffffff, #ffffffff);
    color: #fff;
    display: flex;
    flex-direction: column;
    justify-content: space-between; /* top links + bottom logout */
    transition: all 0.3s;
    z-index: 999;
}

/* Scrollable area for links */
.sidebar-menu {
    overflow-y: auto;
    flex-grow: 1;
    padding-top: 20px;
}

/* Sidebar links */
.sidebar a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #000000ff;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s;
    border-radius: 6px;
    margin: 5px 10px;
}

/* Hover and Active effects */
.sidebar a:hover {
    background: #ff7f50;
    color: #fff;
}
.sidebar a.active {
    background: #4cca69ff;
    color: #fff;
}

/* Sidebar icons */
.sidebar a i {
    margin-right: 10px;
}

/* Logout link style */
.sidebar .logout-link {
    background: rgba(255, 255, 255, 0.1);
    margin: 10px;
    border-radius: 6px;
}
.sidebar .logout-link:hover {
    background: #ff7f50;
}

/* Scrollbar styling */
.sidebar-menu::-webkit-scrollbar {
    width: 6px;
}
.sidebar-menu::-webkit-scrollbar-thumb {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 4px;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        width: 60px;
    }
    .sidebar a span {
        display: none;
    }
}
</style>

<div class="sidebar">
    <!-- Scrollable section -->
    <div class="sidebar-menu">
        <a href="dashboard.php" class="<?= $current_page=='dashboard.php' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> 
            <span>Dashboard</span>
        </a>

        <a href="profile.php" class="<?= $current_page=='profile.php' ? 'active' : '' ?>">
            <i class="bi bi-person-circle"></i> 
            <span>My Profile</span>
        </a>

        <?php if ($user_role === 'admin'): ?> 
            <a href="display.php" class="<?= $current_page=='display.php' ? 'active' : '' ?>">
                <i class="bi bi-people"></i> 
                <span>Registered Users</span>
            </a>

            <a href="add_category.php" class="<?= $current_page=='add_category.php' ? 'active' : '' ?>">
                <i class="bi bi-folder-plus"></i> 
                <span>Add Category</span>
            </a>

            <a href="view_categories.php" class="<?= $current_page=='view_categories.php' ? 'active' : '' ?>">
                <i class="bi bi-folder2-open"></i> 
                <span>View Categories</span>
            </a>
        <?php endif; ?>

        <!-- Common links for all users -->
        <a href="event_create.php" class="<?= $current_page=='event_create.php' ? 'active' : '' ?>">
            <i class="bi bi-plus-circle"></i> 
            <span>Create Event</span>
        </a>

        <a href="add_category.php" class="<?= $current_page=='add_category.php' ? 'active' : '' ?>">
                <i class="bi bi-ticket-perforated"></i> <span>Add Category</span>
        </a>

        
        <a href="my_events.php" class="<?= $current_page=='my_events.php' ? 'active' : '' ?>">
            <i class="bi bi-calendar-event"></i> 
            <span>My Events</span>
        </a>

        <a href="upcoming_events.php" class="<?= $current_page=='upcoming_events.php' ? 'active' : '' ?>">
            <i class="bi bi-calendar-check"></i> 
            <span>Upcoming Events</span>
        </a>

        <a href="my_favt.php" class="<?= $current_page=='my_favt.php' ? 'active' : '' ?>">
            <i class="bi bi-heart"></i> 
            <span>My Favourites</span>
        </a>

        <a href="my_bookings.php" class="<?= $current_page=='my_bookings.php' ? 'active' : '' ?>">
            <i class="bi bi-ticket-perforated"></i> 
            <span>My Bookings</span>
        </a>
    </div>

    <!-- Fixed logout at bottom -->
    <a href="logout.php" class="logout-link">
        <i class="bi bi-box-arrow-right"></i> 
        <span>Logout</span>
    </a>
</div>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
