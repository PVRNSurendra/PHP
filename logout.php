<?php
session_start();
session_destroy();
header("Location: login.php");
exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <style>
         .container {
            text-align: center;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        .links a {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .links a:hover {
            background-color: #45a049;
        }

        .main{
            display: flex;
            flex-direction: column;
        }
     </style>
</head>
<body>
    <div class="main">
        <h1 style="position: relative; left: 35px;">Welcome to the Web Wallet Application</h1>
        <div class="container">
            <div class="links">
                <a href="login.php">Login</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="transfer.php">Transfer Money</a>
                <a href="register.php">Register</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</body>
</html>
