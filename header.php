<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include 'connect.php';

$user_id = $_SESSION['user_id'] ?? null;
$user_name = "Guest";
$user_role = "";
$profile_image = "";

if ($user_id) {
    $stmt = $con->prepare("SELECT name, profile_image, role FROM user_table WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_name = $row['name'];
        $profile_image = $row['profile_image'];
        $user_role = $row['role'];
    }
    $stmt->close();
}

$image_url = "uploads/" . htmlspecialchars($profile_image);
?>

<!-- Navbar -->
<nav class="custom-navbar-full">
    <div class="navbar-container">
        <span class="brand">Event Project</span>
        <ul class="nav-links">
            <?php if(!$user_id): ?>
              <a href="signup.php">Sign Up</a>
              <a href="login.php">Login</a>
            <?php else: ?>
                <li class="dropdown">
                    <a href="#" class="dropbtn profile-btn">
                        <?php if(!empty($profile_image) && file_exists($image_url)): ?>
                            <img src="<?= $image_url ?>" alt="Profile" class="profile-circle">
                        <?php else: ?>
                            <div class="profile-fallback"><?= strtoupper(substr($user_name,0,1)) ?></div>
                        <?php endif; ?>
                        <div class="profile-info">
                            <span class="profile-name"><?= htmlspecialchars($user_name) ?></span>
                            <small class="profile-role"><?= ($user_role === 'admin') ? 'Admin' : 'User'; ?></small>
                        </div>
                    </a>
                  <span class="dropdown-content"> 
                    <a href="profile.php">My Profile</a>
                    <?php if($user_role === 'admin'): ?>
                    <a href="display.php">Registered Users</a>
                        <?php endif; ?> 
                    <a href="logout.php" class="logout">Logout</a>
                 </span>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<style>
/* Navbar */
.custom-navbar-full { background: linear-gradient(135deg, #ffffffff, #ffffffff); padding: 12px 20px; font-family: 'Poppins', sans-serif; position: fixed; width: 100%; top: 0; left: 0; z-index: 1000; box-shadow: 0 4px 10px rgba(209, 118, 118, 0.1); }
.navbar-container { display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: auto; }
.brand { color: #000000ff; font-size: 22px; font-weight: 700; text-decoration: none; }
.nav-links { list-style: none; display: flex; align-items: center; gap: 20px; margin: 0; padding: 0; }
.nav-links li { position: relative; }
.nav-links a { color: #000000ff; text-decoration: none; font-weight: 500; padding: 8px 12px; border-radius: 6px; transition: all 0.3s ease; }
.nav-links a:hover { background: #ff7f00; color: #000; }

/* Stylish Profile */
.profile-btn { display: flex; align-items: center; gap: 10px; padding: 5px 10px; border-radius: 50px; background: rgba(0, 0, 0, 0.1); transition: all 0.3s ease; }
.profile-btn:hover { background: rgba(0, 0, 0, 0.2); }
.profile-info { display: flex; flex-direction: column; line-height: 1.1; }
.profile-name { color: #000000ff; font-weight: 600; max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.profile-role { font-size: 11px; color: #000000ff; margin-top: 2px; }
.profile-circle { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.3); transition: transform 0.2s ease, box-shadow 0.2s ease; }
.profile-circle:hover { transform: scale(1.1); box-shadow: 0 4px 12px rgba(0,0,0,0.4); }
.profile-fallback { width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, #00fe19ff, #ff6a00ff); color: #000000ff; font-weight: bold; display: flex; justify-content: center; align-items: center; font-size: 18px; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.3); transition: transform 0.2s ease, box-shadow 0.2s ease; }
.profile-fallback:hover { transform: scale(1.1); box-shadow: 0 4px 12px rgba(0,0,0,0.4); }

/* Dropdown */
.dropdown-content { display: none; position: absolute; top: 120%; right: 0; background: rgba(255, 255, 255, 1); min-width: 220px; border-radius: 12px; overflow: hidden; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4); z-index: 1000; flex-direction: column; }
.dropdown-content li a { display: block; padding: 12px 18px; font-weight: 500; font-size: 14px; color: #4e3e3eff; transition: all 0.3s ease; }
.dropdown-content li a:hover { background: #000000ca; transform: translateX(5px); }
.dropbtn { cursor: pointer; display: flex; align-items: center; gap: 5px; }
.logout { color: #ff4d4d !important; }

/* Responsive */
@media (max-width: 768px) { .nav-links { flex-direction: column; gap: 8px; } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropbtn = document.querySelector('.dropbtn');
    const dropdown = document.querySelector('.dropdown-content');

    if(dropbtn){
        dropbtn.addEventListener('click', function(e){
            e.preventDefault();
            dropdown.classList.toggle('show');
        });

        window.addEventListener('click', function(e){
            if (!dropbtn.contains(e.target) && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        });
    }
});
</script>
<style>.dropdown-content.show { display: flex; flex-direction: column; }</style>
