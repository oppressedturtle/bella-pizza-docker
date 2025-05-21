<?php
session_start();
require_once 'log_helper.php';

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

if (!isset($_SESSION["employee_id"]) || !in_array($_SESSION["role"], ['admin', 'cashier'])) {
    header("Location: login.php");
    exit();
}

$employee_id = $_SESSION["employee_id"];
$username = $_SESSION["username"] ?? 'Unknown';

$order_id = $_GET['order_id'] ?? null;
$success = $error = "";

if (!$order_id) {
    die("Order ID not provided.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['driver_id'])) {
    $driver_id = $_POST['driver_id'];
    $stmt = $pdo->prepare("UPDATE `order` SET delivery_driver_id = :driver_id WHERE order_id = :order_id");
    if ($stmt->execute([':driver_id' => $driver_id, ':order_id' => $order_id])) {
        $success = "Driver assigned successfully.";

   
        $driver = $pdo->prepare("SELECT first_name, last_name FROM employee WHERE employee_id = ?");
        $driver->execute([$driver_id]);
        $driver_data = $driver->fetch(PDO::FETCH_ASSOC);
        $driver_name = $driver_data ? $driver_data['first_name'] . ' ' . $driver_data['last_name'] : 'Unknown Driver';

        log_action(
            "Assign Driver",
            "Assigned $driver_name to order #$order_id",
            "INFO",
            $employee_id,
            null,
            $username
        );
        
    } else {
        $error = "Failed to assign driver.";
    }
}


$drivers = $pdo
    ->query("SELECT employee_id, first_name, last_name FROM employee WHERE role = 'delivery'")
    ->fetchAll(PDO::FETCH_ASSOC);


$stmt = $pdo->prepare("
    SELECT o.*, c.username AS customer_name
    FROM `order` o
    LEFT JOIN customer c ON o.customer_id = c.customer_id
    WHERE o.order_id = :order_id
");
$stmt->execute([':order_id' => $order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Assign Driver | Bella Pizza</title>
  <style>
    :root {
      --primary: #dc3545;
      --light: #fff;
      --bg: #f4f4f4;
      --card: #fff;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: var(--bg);
      color: #333;
    }
    .header {
      background: var(--primary);
      color: var(--light);
      padding: 12px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .header .logo { font-size: 1.4rem; font-weight: bold; }
    .header nav button {
      background: transparent;
      border: none;
      color: var(--light);
      font-weight: bold;
      margin-left: 16px;
      cursor: pointer;
    }
    .header nav button:hover { text-decoration: underline; }
    .container {
      max-width: 600px;
      margin: 40px auto;
      background: var(--card);
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    .card {
      padding: 20px;
    }
    .card h2 {
      margin-bottom: 15px;
      color: var(--primary);
      text-align: center;
    }
    .message {
      text-align: center;
      margin-bottom: 12px;
      font-weight: bold;
    }
    .message.success { color: green; }
    .message.error { color: red; }
    .order-info {
      background: #fafafa;
      border: 1px solid #eee;
      border-radius: 6px;
      padding: 15px;
      margin-bottom: 20px;
    }
    .order-info p { margin-bottom: 8px; }
    form {
      display: flex;
      flex-direction: column;
    }
    label {
      font-weight: 500;
      margin-bottom: 6px;
    }
    select, button {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
      margin-bottom: 12px;
    }
    button {
      background: var(--primary);
      color: var(--light);
      border: none;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s;
    }
    button:hover { background: #a71d2a; }
    .actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .actions button {
      width: 48%;
    }
    .back-link a {
      display: inline-block;
      text-decoration: none;
      background: #28a745;
      color: var(--light);
      padding: 10px 16px;
      border-radius: 6px;
      font-weight: bold;
      text-align: center;
      width: 100%;
    }
    .back-link a:hover { background: #1f8033; }
    @media (max-width: 480px) {
      .actions { flex-direction: column; }
      .actions button { width: 100%; margin-bottom: 8px; }
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="logo">Bella Pizza 🍕</div>
    <nav>
      <button onclick="location.href='orders.php'"> Back to Orders</button>
    </nav>
  </header>

  <div class="container">
    <div class="card">
      <h2>Assign Driver to Order #<?= htmlspecialchars($order_id) ?></h2>

      <?php if ($success): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
      <?php elseif ($error): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="order-info">
        <p><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?></p>
        <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
        <p><strong>Total:</strong> <?= htmlspecialchars($order['total_amount']) ?> BD</p>
        <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
      </div>

      <form method="POST">
        <label for="driver_id">Select Driver</label>
        <select name="driver_id" id="driver_id" required>
          <option value="">-- Pick a driver --</option>
          <?php foreach ($drivers as $d): ?>
            <option value="<?= $d['employee_id'] ?>">
              <?= htmlspecialchars($d['first_name'] . ' ' . $d['last_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <div class="actions">
          <button type="submit">Assign Driver</button>
          <div class="back-link">
            <a href="orders.php"> Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
