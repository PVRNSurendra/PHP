<?php
session_start();

// Check if the session is set and the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if the session variables are set
if (!isset($_SESSION['username']) || !isset($_SESSION['balance'])) {
    echo "Error: Session variables not set.";
    exit();
}

include 'db.php';

// Set the user_id from the session
$user_id = $_SESSION['user_id'];

// Prepare the SQL statement to fetch transactions
$stmt = $pdo->prepare("SELECT t.amount, t.transaction_date, u_sender.username AS sender, u_receiver.username AS receiver 
                       FROM transactions t
                       LEFT JOIN users u_sender ON t.sender_id = u_sender.id
                       LEFT JOIN users u_receiver ON t.receiver_id = u_receiver.id
                       WHERE t.sender_id = :user_id OR t.receiver_id = :user_id
                       ORDER BY t.transaction_date DESC");
$stmt->execute(['user_id' => $user_id]);
$transactions = $stmt->fetchAll();  // Fetch all transactions

// Define conversion rate
$conversionRate = 1;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        .container {
            text-align: center;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
            width: 80%; /* Adjust the width as per your preference */
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

        .main {
            display: flex;
            flex-direction: column;
            align-items: center; /* Centering content horizontally */
        }

        table {
            width: 80%; /* Adjust width as necessary */
            margin: 20px auto; /* This centers the table */
            border-collapse: collapse; /* Optional: for better border styling */
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd; /* Optional: for better table border styling */
        }

        th {
            background-color: #f4f4f4; /* Optional: makes header row distinct */
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
    <h2>Dashboard</h2>
    <p>Welcome, <?php echo $_SESSION['username']; ?></p>
    <p>Balance: ₹<?php echo number_format($_SESSION['balance'] * $conversionRate, 2); ?></p>

    <h3>Transaction Statement</h3>
    <table>
        <tr>
            <th>From</th>
            <th>To</th>
            <th>Amount</th>
            <th>Type</th>
            <th>Date</th>
        </tr>
        <?php if (!empty($transactions)): ?>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <td><?php echo $transaction['sender']; ?></td>
                    <td><?php echo $transaction['receiver']; ?></td>
                    <td>₹<?php echo number_format($transaction['amount'] * $conversionRate, 2); ?></td>
                    <td>
                        <?php echo $transaction['sender'] == $_SESSION['username'] ? 'Debit' : 'Credit'; ?>
                    </td>
                    <td><?php echo date('Y-m-d', strtotime($transaction['transaction_date'])); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No transactions found.</td>
            </tr>
        <?php endif; ?>
    </table>

    <a href="transfer.php">Transfer Money</a>
    <a href="logout.php">Logout</a>
</div>
</body>
</html>
