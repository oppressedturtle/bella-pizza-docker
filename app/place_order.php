<?php
require_once 'includes/session_config.php';
require_once 'includes/csrf.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["from_payment"])) {
    header("Location: dashboard_user.php");
    exit();
}

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION["cart"]) || empty($_SESSION["cart"])) {
    header("Location: dashboard_user.php");
    exit();
}

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

$cart = $_SESSION["cart"];
$menu_ids = array_keys($cart);
$placeholders = implode(",", array_fill(0, count($menu_ids), "?"));
$stmt = $pdo->prepare("SELECT menu_id, price FROM menu WHERE menu_id IN ($placeholders)");
$stmt->execute($menu_ids);
$prices = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); 

$total = 0;
$order_items = [];

foreach ($cart as $menu_id => $qty) {
    $price = $prices[$menu_id];
    $subtotal = $qty * $price;
    $total += $subtotal;
    $order_items[] = [
        'menu_id' => $menu_id,
        'quantity' => $qty,
        'subtotal' => $subtotal
    ];
}


$login_type = $_SESSION["login_type"] ?? 'normal';


$stmt = $pdo->prepare("INSERT INTO `order` (customer_id, status, total_amount, login_type) VALUES (?, 'Pending', ?, ?)");
$stmt->execute([$_SESSION["user_id"], $total, $login_type]);
$order_id = $pdo->lastInsertId();


$stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
foreach ($order_items as $item) {
    $stmt->execute([$order_id, $item['menu_id'], $item['quantity'], $item['subtotal']]);
}


unset($_SESSION["cart"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 40px 20px;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            text-align: center;
        }

        h2 {
            color: #28a745;
            margin-bottom: 10px;
        }

        p {
            font-size: 18px;
            margin: 10px 0;
        }

        .total {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
        }

        a {
            display: inline-block;
            margin-top: 25px;
            padding: 10px 18px;
            background-color: #007bff;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🎉 Order Placed Successfully!</h2>
        <p>Your order <strong>#<?= $order_id ?></strong> has been submitted. Thank you for ordering with Bella Pizza!</p>
        <p class="total">Total Paid: <?= number_format($total, 3) ?> BD</p>
        <a href="dashboard_user.php"> Back to Homepage</a>
    </div>
</body>
</html>
