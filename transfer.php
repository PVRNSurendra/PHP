<?php
session_start();
include 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $receiver_username = trim($_POST['receiver_username']);
    $amount = floatval($_POST['amount']); 

    $stmt = $pdo->prepare("SELECT id, balance FROM users WHERE username = :username");
    $stmt->execute(['username' => $receiver_username]);
    $receiver = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($receiver && $amount > 0 && $_SESSION['balance'] >= $amount) {
        $pdo->beginTransaction(); 

        try {

            $stmt = $pdo->prepare("UPDATE users SET balance = balance - :amount WHERE id = :id");
            $stmt->execute(['amount' => $amount, 'id' => $_SESSION['user_id']]);

            $stmt = $pdo->prepare("UPDATE users SET balance = balance + :amount WHERE id = :receiver_id");
            $stmt->execute(['amount' => $amount, 'receiver_id' => $receiver['id']]);

            $stmt = $pdo->prepare("INSERT INTO transactions (sender_id, receiver_id, amount) VALUES (:sender_id, :receiver_id, :amount)");
            $stmt->execute(['sender_id' => $_SESSION['user_id'], 'receiver_id' => $receiver['id'], 'amount' => $amount]);

            $_SESSION['balance'] -= $amount;

            $pdo->commit();
            echo "<p style='color:green;'>Transfer successful!</p>";
        } catch (Exception $e) {
            $pdo->rollBack(); 
            echo "<p style='color:red;'>Transaction failed: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p style='color:red;'>Invalid transfer details.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transfer Money</title>
    <!-- <link rel="stylesheet" href="style.css"> -->
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

    <div class="container">
        <h2>Transfer Money</h2>
        <form method="POST" action="">
            <input type="text" name="receiver_username" placeholder="Recipient Username" required><br>
            <input type="number" name="amount" placeholder="Amount" required min="1"><br>
            <button type="submit">Transfer</button>
        </form>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
