<?php
ob_start();
session_start();
include 'connect.php';
include 'header.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    $input = trim($_POST['username']); // username or email
    $password = $_POST['password'];

    if ($input == '' || $password == '') {
        $error = "Please enter both username/email and password.";
    } else {
        // find user
        
        $stmt = $con->prepare("SELECT user_id, username, email, password, role FROM user_table WHERE username=? OR email=? LIMIT 1");
        
        $stmt->bind_param('ss', $input, $input);
        $stmt->execute();
        $result = $stmt->get_result();
        

        if ($result->num_rows == 0) {
            $error = "User not found.";
        } else {
            $user = $result->fetch_assoc();
            
            // simple text compare
            if ($password == $user['password'] && $input == $user['username']) {
                $_SESSION['user_id']  = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];

                // update login_date
                $uid = $user['user_id'];
                $con->query("UPDATE user_table SET login_date = NOW() WHERE user_id = $uid");

                // redirect by role
                    header('Location: dashboard.php');
            } else {
                $error = "Wrong password.";
            }
        }
    }
}
?>
<!-- HTML part (below) -->
<!doctype html>
<html>
<head>
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
  background: linear-gradient(135deg, #ffffffff, #ffffffff);
  min-height: 100vh;
  display: block;
  align-items: flex-start; /* 👈 ab form top se start hoga */
  justify-content: center;
  padding: 40px 20px;      /* upar se thoda gap */
  overflow-y: auto;        /* 👈 agar form bada hoga to scroll aa jayega */
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
    body {
    opacity: 0;
    animation: fadeIn 2s forwards; /* 1 second me fade-in */
}

@keyframes fadeIn {
    to { opacity: 1; }
}


  </style>
</head>
<body>
   <div class="container d-flex justify-content-center align-items-start mt-5">
    <div class="col-md-6 col-lg-5 animate__animated animate__fadeIn">
      <div class="card">
        <div class="card-header"> Authentication </div>
        <div class="card-body p-4">

         <?php if(!empty($error)): ?>
              <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

      <form method="post" autocomplete="off">
  <div class="mb-3">
    <label class="form-label">Username or Email</label>
    <input type="text" class="form-control" name="username" placeholder="Enter your username or email"
           autocomplete="new-username">
  </div>

  <div class="mb-3">
    <label class="form-label">Password</label>
    <input type="password" class="form-control" name="password" placeholder="Enter your password"
           autocomplete="new-password">
  </div>

  <button class="btn btn-primary w-100" type="submit">Login</button>
</form>
         <div class="mt-3 text-center">
              <a href="signup.php">Create account</a>
            </div>
        </form>
      </div>
    </div>
  </div>
  </div>
   <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
