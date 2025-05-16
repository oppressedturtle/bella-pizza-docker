<?php

$host = "db";
$dbname = "RestaurantDB";
$username = "root";
$password = "rootpass"; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if (!isset($_GET['order_id'])) {
    die("Order ID is required.");
}

$order_id = $_GET['order_id'];

// First, get the order with login_type and customer_id
$order_stmt = $pdo->prepare("SELECT * FROM `order` WHERE order_id = ?");
$order_stmt->execute([$order_id]);
$order = $order_stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}

// Now fetch customer details based on login_type
$customer = null;

if ($order['login_type'] === 'google') {
    $cust_stmt = $pdo->prepare("SELECT username AS customer_name, email, phone, address FROM google_login WHERE customer_id = ?");
} else {
    $cust_stmt = $pdo->prepare("SELECT username AS customer_name, email, phone, address FROM customer WHERE customer_id = ?");
}

$cust_stmt->execute([$order['customer_id']]);
$customer = $cust_stmt->fetch(PDO::FETCH_ASSOC);

// Merge into order for display convenience
$order = array_merge($order, $customer ?: []);

$items_stmt = $pdo->prepare("
    SELECT oi.*, m.item_name, m.price
    FROM order_items oi
    JOIN menu m ON oi.menu_id = m.menu_id
    WHERE oi.order_id = ?
");
$items_stmt->execute([$order_id]);
$order_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?= $order_id ?> Details</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url("images/269.jpg") repeat;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 40px;
        }

        .container {
            width: 900px;
            background: #fff;
            border-radius: 12px;
            padding: 30px 40px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        h2, h3 {
            text-align: center;
            color: #dc3545;
            margin-bottom: 20px;
        }

        p {
            margin: 6px 0;
            color: #333;
            font-size: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #dc3545;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .back-link {
            text-align: center;
            margin-top: 30px;
        }

        .back-link a {
            text-decoration: none;
            padding: 10px 16px;
            background-color: #dc3545;
            color: white;
            font-weight: bold;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .back-link a:hover {
            background-color: #a71d2a;
        }

        .section {
            margin-bottom: 30px;
        }

        .section p strong {
            width: 150px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Order #<?= $order_id ?> Details</h2>

        <div class="section">
            <h3>🧾 Order Info</h3>
            <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
            <p><strong>Total Amount:</strong> <?= htmlspecialchars($order['total_amount']) ?> BD</p>
            <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
        </div>

        <div class="section">
            <h3>👤 Customer Info</h3>
            <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($order['email'] ?? '-') ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone'] ?? '-') ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($order['address'] ?? '-') ?></p>
        </div>

        <div class="section">
            <h3>🍕 Items</h3>
            <?php if (count($order_items) > 0): ?>
                <table>
                    <tr>
                        <th>Item</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                            <td><?= number_format($item['price'], 2) ?> BD</td>
                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                            <td><?= number_format($item['subtotal'], 2) ?> BD</td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No items found in this order.</p>
            <?php endif; ?>
        </div>

        <div class="back-link">
            <a href="orders.php">← Back to Orders</a>
        </div>
    </div>
</body>
</html>
