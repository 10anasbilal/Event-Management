<?php
include 'connect.php';
include 'header.php';

$error = ''; // error message ke liye variable

if (isset($_POST['submit'])) {
    $name     = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $mobile   = trim($_POST['mobile']);
    $raw_pass = $_POST['password'];
    $address  = trim($_POST['address']);
    $role=$_POST['role']?$_POST['role']:'user';

    // ✅ Username Validation (sirf letters, numbers, underscore allowed)
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = "❌ Username can only contain letters, numbers, and underscores ( _ ). Special characters are not allowed!";
    }

    // agar koi error nahi to database check karein
    if (empty($error)) {
        // duplicate check
        $checkUser  = mysqli_query($con, "SELECT 1 FROM user_table WHERE username = '$username' LIMIT 1");
        $checkEmail = mysqli_query($con, "SELECT 1 FROM user_table WHERE email = '$email' LIMIT 1");

        if (mysqli_num_rows($checkUser) > 0) {
            $error = "❌ Username already taken. Please choose another.";
        } elseif (mysqli_num_rows($checkEmail) > 0) {
            $error = "❌ Email already registered. Try logging in.";
        } else {
            // insert data
            $sql = "INSERT INTO user_table 
                    (name, username, email, mobile, password, role, address, profile_image, login_date)
                    VALUES 
                    ('$name','$username','$email','$mobile','$raw_pass','$role','$address','',NOW())";

            if (mysqli_query($con, $sql)) {
                echo "<script>alert('✅ Signup Successful!'); window.location='login.php';</script>";
                exit;
            } else {
                $error = "Database Error: " . mysqli_error($con);
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
  <title>User Signup</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Animate.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

  <style>
  body {
  background: linear-gradient(135deg, #ffffffff, #ffffffff);
  min-height: 100vh;
  display: block;
  align-items: flex-start;   /* 👈 upar se start hoga */
  justify-content: center;   /* 👈 horizontally center */
  padding: 40px 20px;
  overflow-y: auto;
}
    .card {
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }
    .card-header {
      background: #000000ff;
      color: #fff;
      text-align: center;
      font-size: 20px;
      font-weight: 600;
    }
    .btn-custom {
      background: #6a11cb;
      color: #fff;
      font-weight: 500;
    }
    .btn-custom:hover {
      background: #2575fc;
    }
    input, textarea, select {
      background-color: #f9f9f9 !important;
    }
     /* animation */
.card {
    transform: scale(0);
    animation: popUp 2s forwards;
}
@keyframes popUp {
    to { transform: scale(1); }
}

  </style>
</head>
<body>

  <div class="container d-flex justify-content-center align-items-start mt-5">
    <div class="col-md-6 col-lg-5 animate__animated animate__fadeIn">
      <div class="card">
        <div class="card-header"> Join Us </div>
        <div class="card-body p-4">

     <form method="post" action="signup.php" enctype="multipart/form-data" autocomplete="off">

  <div class="mb-3">
    <label class="form-label">Full Name</label>
    <input type="text" class="form-control" name="name" 
           value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" 
           placeholder="Enter your full name" required>
  </div>

  <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" class="form-control" name="username" 
               value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" 
               placeholder="Choose a unique username" required>
      </div>

      <!-- ✅ Error message -->
  <?php if (!empty($error)): ?>
    <div class="alert alert-danger">
      <?= $error ?>
    </div>
  <?php endif; ?>

  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" class="form-control" name="email" 
           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" 
           placeholder="Enter your email" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Mobile</label>
    <input type="tel" class="form-control" name="mobile" 
           value="<?= isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : '' ?>" 
           placeholder="Enter your mobile number" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Password</label>
    <input type="password" class="form-control" name="password" 
           placeholder="Enter password" required autocomplete="new-password">
  </div>

  <div class="mb-3">
    <label class="form-label">Address</label>
    <textarea class="form-control" name="address"><?= isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '' ?></textarea>
  </div>

  <div class="mb-3">
    <label class="form-label">Role</label>
    <select class="form-select" name="role">
      <option value="user" <?= (isset($_POST['role']) && $_POST['role'] == 'user') ? 'selected' : '' ?>>User</option>
       <option value="admin" <?= (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">Profile Image</label>
    <input type="file" class="form-control" name="profile_image">
  </div>

  <button type="submit" name="submit" class="btn btn-custom w-100">Register</button>
</form>

      </div>
    </div>
  </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

