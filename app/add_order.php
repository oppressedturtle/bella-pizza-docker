<?php
session_start();
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

// Fetch customers and menu items
$customers = $pdo->query("SELECT customer_id, username FROM customer ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);
$menu_items = $pdo->query("SELECT menu_id, item_name, price FROM menu WHERE availability = 1")->fetchAll(PDO::FETCH_ASSOC);

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'];
    $items = $_POST['items']; // array of menu_id => quantity

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
        // Insert into `order`
        $stmt = $pdo->prepare("INSERT INTO `order` (customer_id, total_amount) VALUES (?, ?)");
        $stmt->execute([$customer_id, $total_amount]);
        $order_id = $pdo->lastInsertId();

        // Insert into order_items
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
</head>
<body>
    <h2>Create New Order</h2>

    <?php if (!empty($success)) echo "<p style='color: green;'>$success</p>"; ?>
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>

    <form method="POST">
        <label>Customer:
            <select name="customer_id" required>
                <option value="">-- Select Customer --</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?= $customer['customer_id'] ?>"><?= htmlspecialchars($customer['username']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <br><br>

        <h3>Menu Items</h3>
        <?php foreach ($menu_items as $item): ?>
            <div>
                <label>
                    <?= htmlspecialchars($item['item_name']) ?> (<?= $item['price'] ?> BD):
                    <input type="number" name="items[<?= $item['menu_id'] ?>]" value="0" min="0">
                </label>
            </div>
        <?php endforeach; ?>

        <br>
        <button type="submit">Place Order</button>
    </form>

    <br>
    <a href="dashboard.php">← Back to Dashboard</a>
</body>
</html>
