<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['order_id'])) {
    die("Order ID is required.");
}

$order_id = $_GET['order_id'];

$pdo = new PDO("mysql:host=db;dbname=RestaurantDB", "root", "rootpass");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$stmt = $pdo->prepare("SELECT * FROM `order` WHERE order_id = ? AND customer_id = ?");
$stmt->execute([$order_id, $_SESSION["user_id"]]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found or access denied.");
}


$stmt = $pdo->prepare("SELECT oi.quantity, m.item_name, m.price FROM order_items oi JOIN menu m ON oi.menu_id = m.menu_id WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?= htmlspecialchars($order_id) ?> Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #dc3545;
            --accent: #f8c102;
            --bg: #fff8f3;
            --light: #fff;
        }
        html, body {
    margin: 0;
    height: 100vh;
    font-family: 'Fredoka', sans-serif;
    background: var(--bg);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0;
}


        .container {
            width: 100%;
            max-width: 900px;
            background: var(--light);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .back-button {
            text-align: left;
            margin-bottom: 20px;
        }

        .back-link {
            background-color: var(--primary);
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .back-link:hover {
            background-color: #a71d2a;
        }

        h2 {
            text-align: center;
            margin-top: 0;
            color: var(--primary);
        }

        .info {
            margin-bottom: 20px;
            font-size: 16px;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--primary);
            color: white;
        }

        tr:hover {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="back-button">
        <a href="my_orders.php" class="back-link">← Back to My Orders</a>
    </div>

    <h2>Order #<?= htmlspecialchars($order_id) ?> Details</h2>

    <div class="info">
        <p><strong>Order Date:</strong> <?= htmlspecialchars(date('d M Y, H:i', strtotime($order['order_date']))) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
        <p><strong>Total Amount:</strong> <?= number_format($order['total_amount'], 2) ?> BD</p>
    </div>

    <?php if (count($items) > 0): ?>
        <table>
            <tr>
                <th>Item</th>
                <th>Price (BD)</th>
                <th>Quantity</th>
                <th>Subtotal (BD)</th>
            </tr>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['item_name']) ?></td>
                    <td><?= number_format($item['price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p style="text-align:center; margin-top:20px;">No items found for this order.</p>
    <?php endif; ?>
</div>
</body>
</html>