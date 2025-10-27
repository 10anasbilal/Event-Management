<?php
session_start();
include 'connect.php';
include 'header.php';
include 'sidebar.php';

// Login check
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if($_SESSION['role'] != 'admin'){
    $_SESSION['error'] = "❌ Only Admin can see this page!";
    header("Location: dashboard.php");
    exit();
}

// SQL query
$sql = "SELECT user_id, name, username, email, mobile, role, address FROM user_table";
$result = mysqli_query($con, $sql);

if(!$result){
    die("Query Failed: " . mysqli_error($con));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registered Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 0;
            margin: 0;
            font-family: "Poppins", sans-serif;
            background: #f8f9fa;
        }

        /* Container adjustment with sidebar */
        .container {
            margin-left: 260px; /* Sidebar width */
            margin-top: 80px;   /* Header height */
            padding: 20px;
            width: calc(100% - 260px);
            overflow-x: hidden; /* Horizontal scroll remove */
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px; /* Table font slightly smaller for full view */
        }

        th, td {
            padding: 8px 10px;
            text-align: center;
            border: 1px solid #dee2e6;
        }

        th {
            background: #e2e8f0;
        }

        .butt {
            display: inline-block;
            margin: 2px;
            padding: 6px 14px;
            text-decoration: none;
            color: #fff;
            border-radius: 6px;
            font-weight: 500;
            transition: 0.3s;
        }

        .edit { background-color: green; }
        .delete { background-color: red; }

        /* Table animation */
        .table {
            transform: translateY(50px);
            opacity: 0;
            animation: slideUp 0.6s forwards;
        }

        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Responsive */
        @media(max-width:768px){
            .container {
                margin-left: 0;
                width: 100%;
                padding: 10px;
            }
            table, thead, tbody, th, td, tr {
                display: block;
                width: 100%;
            }
            th, td {
                text-align: left;
            }
            td {
                padding-left: 50%;
                position: relative;
            }
            td::before {
                position: absolute;
                left: 10px;
                width: 45%;
                white-space: nowrap;
                font-weight: bold;
                text-align: left;
            }
            td:nth-of-type(1)::before { content: "ID"; }
            td:nth-of-type(2)::before { content: "Name"; }
            td:nth-of-type(3)::before { content: "Username"; }
            td:nth-of-type(4)::before { content: "Email"; }
            td:nth-of-type(5)::before { content: "Mobile"; }
            td:nth-of-type(6)::before { content: "Role"; }
            td:nth-of-type(7)::before { content: "Address"; }
            td:nth-of-type(8)::before { content: "Action"; }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Registered Users</h2>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Role</th>
                <th>Address</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>".htmlspecialchars($row['user_id'])."</td>
                            <td>".htmlspecialchars($row['name'])."</td>
                            <td>".htmlspecialchars($row['username'])."</td>
                            <td>".htmlspecialchars($row['email'])."</td>
                            <td>".htmlspecialchars($row['mobile'])."</td>
                            <td>".htmlspecialchars($row['role'])."</td>
                            <td>".htmlspecialchars($row['address'])."</td>
                            <td>
                                <a href='edit.php?id=".$row['user_id']."' class='butt edit'>Edit</a>
                                <a href='delete.php?id=".$row['user_id']."' class='butt delete' onclick='return confirm(\"Press OK to delete, or Cancel to go back.\");'>Delete</a>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No users found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
