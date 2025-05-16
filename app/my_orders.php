<?php
require_once 'includes/session_config.php';
require_once 'includes/csrf.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$pdo = new PDO("mysql:host=db;dbname=RestaurantDB", "root", "rootpass");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user_id = $_SESSION["user_id"];
$login_type = $_SESSION["login_type"] ?? 'normal'; // fallback

// Fetch username from correct table
if ($login_type === 'google') {
    $stmt = $pdo->prepare("SELECT username FROM google_login WHERE customer_id = ?");
} else {
    $stmt = $pdo->prepare("SELECT username FROM customer WHERE customer_id = ?");
}
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch all orders for user
$sql = "SELECT * FROM `order` WHERE customer_id = :customer_id AND login_type = :login_type ORDER BY order_date DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':customer_id' => $user_id,
    ':login_type' => $login_type
]);

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Optional: filter
$search = $_GET['search'] ?? '';
if (!empty($search)) {
    $orders = array_filter($orders, function ($order) use ($search) {
        return stripos((string)$order['order_id'], $search) !== false ||
               stripos($order['status'], $search) !== false ||
               stripos($order['order_date'], $search) !== false;
    });
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - Bella Pizza</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #dc3545;
            --accent: #f8c102;
            --light: #fff;
            --dark: #222;
            --bg: #fff8f3;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Fredoka', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            padding: 0;
            margin: 0;
        }

        .navbar {
            background: var(--primary);
            color: var(--light);
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            font-size: 1.5rem;
            margin: 0;
        }

        .navbar .actions button {
            background: var(--accent);
            color: black;
            border: none;
            padding: 8px 16px;
            font-weight: bold;
            border-radius: 6px;
            margin-left: 10px;
            cursor: pointer;
        }

        .container {
            background: var(--light);
            border-radius: 12px;
            padding: 30px;
            max-width: 1000px;
            width: 100%;
            box-shadow: 0 6px 12px rgba(0,0,0,0.08);
            margin: 30px auto;
        }

        .back-button {
            margin-bottom: 20px;
        }

        .back-link {
            background-color: var(--primary);
            color: var(--light);
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background 0.2s ease;
        }

        .back-link:hover {
            background-color: #a71d2a;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--dark);
        }

        .search-form {
            text-align: center;
            margin-bottom: 25px;
        }

        .search-form input[type="text"] {
            padding: 8px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            width: 250px;
            font-size: 14px;
        }

        .search-form button {
            padding: 8px 14px;
            background-color: var(--primary);
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 6px;
            margin-left: 8px;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #a71d2a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: var(--primary);
            color: var(--light);
        }

        tr:hover {
            background-color: #fdf2f2;
        }

        .status-badge {
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 13px;
            color: white;
            font-weight: bold;
        }

        .Pending {
            background-color: #ffc107;
            color: black;
        }

        .Preparing {
            background-color: #fd7e14;
        }

        .OutforDelivery {
            background-color: #17a2b8;
        }

        .Completed {
            background-color: #28a745;
        }

        .Cancelled {
            background-color: #dc3545;
        }

        .view-button button {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            display: block;
            width: 100%;
            margin-bottom: 8px; 
}


        .view-button button:hover {
            background-color: #a71d2a;
        }

        @media (max-width: 700px) {
            .container {
                padding: 20px;
            }

            th, td {
                font-size: 14px;
                padding: 10px;
            }

            .search-form input[type="text"] {
                width: 100%;
                margin-bottom: 10px;
            }

            .search-form button {
                width: 100%;
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
<div class="navbar">
    <h1>Bella Pizza 🍕</h1>
    <div class="actions">
        <button onclick="location.href='dashboard_user.php'">Dashboard</button>
        <button onclick="location.href='logout.php'">Logout</button>
    </div>
</div>

<div class="container">
  

    <h2>My Orders</h2>

    <?php if (count($orders) > 0): ?>
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search by ID, Status, or Date" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>

        <table>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Status</th>
                <th>Total (BD)</th>
                <th>Action</th>
            </tr>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= htmlspecialchars($order['order_id']) ?></td>
                    <td><?= htmlspecialchars(date('d M Y, H:i', strtotime($order['order_date']))) ?></td>
                    <td>
                        <span class="status-badge <?= str_replace(' ', '', htmlspecialchars($order['status'])) ?>">
                            <?= htmlspecialchars($order['status']) ?>
                        </span>
                    </td>
                    <td><?= number_format($order['total_amount'], 2) ?> BD</td>
                    <td class="view-button">
    <div>
        <form method="GET" action="order_details_user.php" style="margin: 0;">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
            <button type="submit">View Details</button>
        </form>
    </div>
<?php if ($order['status'] === 'Out for Delivery' && !empty($order['delivery_driver_id'])): ?>
    <form method="GET" action="track_delivery.php" style="margin: 0;">
        <input type="hidden" name="order_id" value="<?= (int)$order['order_id'] ?>">
        <input type="hidden" name="driver_id" value="<?= (int)$order['delivery_driver_id'] ?>">
        <button type="submit">Track Delivery</button>
    </form>
<?php endif; ?>

</td>


                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p style="text-align:center; margin-top:20px;">You have not placed any orders yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
