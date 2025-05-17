<?php
require_once 'includes/session_config.php';
require_once 'includes/csrf.php';

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


$customers = $pdo->query("SELECT customer_id, username FROM customer ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT category_id, name FROM category ORDER BY display_order ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC);


$menu_items_by_category = [];
foreach ($categories as $category) {
    $stmt = $pdo->prepare("SELECT menu_id, item_name, price FROM menu WHERE availability = 1 AND category_id = ?");
    $stmt->execute([$category['category_id']]);
    $menu_items_by_category[$category['name']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf_token();

    $customer_id = $_POST['customer_id'];
    $items = $_POST['items'];

    $total_amount = 0;
    $order_items_data = [];

    foreach ($items as $menu_id => $qty) {
        if ($qty > 0) {
            $stmt = $pdo->prepare("SELECT price FROM menu WHERE menu_id = ?");
            $stmt->execute([$menu_id]);
            $price = $stmt->fetchColumn();
            $subtotal = $qty * $price;
            $total_amount += $subtotal;

            $order_items_data[] = [
                'menu_id' => $menu_id,
                'quantity' => $qty,
                'subtotal' => $subtotal
            ];
        }
    }

    if ($total_amount > 0) {
        $stmt = $pdo->prepare("INSERT INTO `order` (customer_id, total_amount) VALUES (?, ?)");
        $stmt->execute([$customer_id, $total_amount]);
        $order_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
        foreach ($order_items_data as $item) {
            $stmt->execute([$order_id, $item['menu_id'], $item['quantity'], $item['subtotal']]);
        }

        $success = "Order successfully created!";
    } else {
        $error = "No items selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Order</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary: #dc3545;
            --accent: #f8c102;
            --light: #fff;
            --bg: #fff8f3;
        }
        body {
            font-family: Arial, sans-serif;
            background: var(--bg);
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            background: var(--light);
            border-radius: 16px;
            padding: 30px;
            width: 100%;
            max-width: 800px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        h2 {
            color: var(--primary);
            margin-bottom: 20px;
            text-align: center;
        }
        label, input, select {
            display: block;
            width: 100%;
            margin-bottom: 15px;
        }
        select, input[type="number"], input[type="text"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        button {
            background: var(--primary);
            color: var(--light);
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #a71d2a;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .success { color: green; }
        .error { color: red; }
        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: var(--primary);
            text-decoration: none;
        }
        details {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            background-color: #fafafa;
        }
        summary {
            cursor: pointer;
            font-weight: bold;
            color: var(--primary);
        }
    </style>
    <script>
        function filterCustomers() {
            const input = document.getElementById('searchCustomer').value.toLowerCase();
            const options = document.getElementById('customerSelect').options;
            for (let i = 0; i < options.length; i++) {
                const text = options[i].text.toLowerCase();
                options[i].style.display = text.includes(input) ? '' : 'none';
            }
        }
    </script>
</head>
<body>
    <div class="card">
        <h2>Create New Order</h2>

        <?php if (!empty($success)): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php elseif (!empty($error)): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <label for="searchCustomer">Search Customer:</label>
            <input type="text" id="searchCustomer" onkeyup="filterCustomers()" placeholder="Type to search...">

            <label for="customerSelect">Customer:</label>
            <select name="customer_id" id="customerSelect" required>
                <option value="">-- Select Customer --</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?= $customer['customer_id'] ?>">
                        <?= htmlspecialchars($customer['username']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <h3>Menu Items</h3>
            <?php foreach ($menu_items_by_category as $category_name => $items): ?>
                <details>
                    <summary><?= htmlspecialchars($category_name) ?></summary>
                    <?php foreach ($items as $item): ?>
                        <label>
                            <?= htmlspecialchars($item['item_name']) ?> (<?= $item['price'] ?> BD)
                            <input type="number" name="items[<?= $item['menu_id'] ?>]" min="0" value="0">
                        </label>
                    <?php endforeach; ?>
                </details>
            <?php endforeach; ?>

            <button type="submit">Place Order</button>
        </form>

        <a class="back-link" href="dashboard.php">← Back to Dashboard</a>
    </div>
</body>
</html>
