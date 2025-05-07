<?php
require_once 'includes/session_config.php';
require_once 'includes/csrf.php';
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

if (!isset($_SESSION['employee_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$employee_id = $_SESSION['employee_id'];
$role = $_SESSION['role'];
$today = date('Y-m-d');

function fetchOrders($pdo, $employee_id, $role) {
    if ($role === 'delivery') {
        $stmt = $pdo->prepare("SELECT o.*, c.username AS customer_name, CONCAT(e.first_name, ' ', e.last_name) AS driver_name FROM `order` o LEFT JOIN customer c ON o.customer_id = c.customer_id LEFT JOIN employee e ON o.delivery_driver_id = e.employee_id WHERE o.delivery_driver_id = :employee_id ORDER BY o.order_date DESC");
        $stmt->execute([':employee_id' => $employee_id]);
    } else {
        $stmt = $pdo->query("SELECT o.*, c.username AS customer_name, CONCAT(e.first_name, ' ', e.last_name) AS driver_name FROM `order` o LEFT JOIN customer c ON o.customer_id = c.customer_id LEFT JOIN employee e ON o.delivery_driver_id = e.employee_id ORDER BY o.order_date DESC");
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$orders = fetchOrders($pdo, $employee_id, $role);
$today_orders = array_filter($orders, fn($o) => date('Y-m-d', strtotime($o['order_date'])) === $today);
$past_orders = array_filter($orders, fn($o) => date('Y-m-d', strtotime($o['order_date'])) !== $today);

function statusBtn($id, $status, $class = '') {
    $token = $_SESSION['csrf_token'];
    return "<form method='POST' class='status-form'>
              <input type='hidden' name='csrf_token' value='$token'>
              <input type='hidden' name='order_id' value='$id'>
              <input type='hidden' name='new_status' value='$status'>
              <button class='$class' type='submit'>$status</button>
            </form>";
}

function linkBtn($url, $id, $label, $class = '') {
    return "<form method='GET' action='$url'>
              <input type='hidden' name='order_id' value='$id'>
              <button class='$class' type='submit'>$label</button>
            </form>";
}

function renderSection($title, $orders, $role) {
    echo "<details open><summary>$title (" . count($orders) . ")</summary>";
    if ($orders) {
        echo "<table><tr><th>Order ID</th><th>Customer</th><th>Date</th><th>Status</th><th>Total</th><th>Driver</th><th>Actions</th></tr>";
        foreach ($orders as $o) {
            echo "<tr>
              <td>" . htmlspecialchars($o['order_id']) . "</td>
              <td class='searchable'>" . htmlspecialchars($o['customer_name']) . "</td>
              <td>" . htmlspecialchars($o['order_date']) . "</td>
              <td><span class='status " . str_replace(' ', '_', $o['status']) . "'>" . htmlspecialchars($o['status']) . "</span></td>
              <td>" . number_format($o['total_amount'], 2) . " BD</td>
              <td>" . ($o['driver_name'] ? htmlspecialchars($o['driver_name']) : '<em>Not assigned</em>') . "</td>
              <td class='actions'>";

            if (!in_array($o['status'], ['Completed', 'Cancelled'])) {
                if ($role === 'cashier') {
                    if ($o['status'] === 'Pending') {
                        echo statusBtn($o['order_id'], 'Preparing');
                    } elseif ($o['status'] === 'Preparing') {
                        echo statusBtn($o['order_id'], 'Out for Delivery');
                        echo linkBtn('assign_driver.php', $o['order_id'], 'Assign Driver', 'btn-outline');
                    }
                }
                if ($role === 'delivery' && $o['status'] === 'Out for Delivery') {
                    echo statusBtn($o['order_id'], 'Completed');
                }
                echo statusBtn($o['order_id'], 'Cancelled', 'btn-outline');
            }

            echo linkBtn('order_details.php', $o['order_id'], 'Details', 'btn-outline');
            echo "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No orders found.</p>";
    }
    echo "</details>";
}

function renderSections($today_orders, $past_orders, $role) {
    renderSection("📅 Today's Orders", $today_orders, $role);
    renderSection("📂 Past Orders", $past_orders, $role);
}

// Handle status change (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    validate_csrf_token();
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];

    $stmt = $pdo->prepare("UPDATE `order` SET status = :status WHERE order_id = :order_id");
    $stmt->execute([
        ':status' => $new_status,
        ':order_id' => $order_id
    ]);

    log_action(
        ($new_status === 'Cancelled' ? "Order Cancelled" : "Order Status Change"),
        "Order #$order_id marked as '$new_status'",
        "WARNING",
        $_SESSION['employee_id'],
        null,
        $_SESSION['username'] ?? 'unknown'
    );

    exit('success');
}


if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    renderSections($today_orders, $past_orders, $role);
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Orders</title>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    :root {
      --primary: #dc3545;
      --accent: #f8c102;
      --light: #fff;
      --bg: #fff8f3;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Fredoka', sans-serif;
      background: var(--bg);
      color: #333;
    }
    .container {
      max-width: 1100px;
      margin: 80px auto 30px;
      background: var(--light);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: var(--primary);
      margin-bottom: 20px;
    }
    .search-form {
      text-align: center;
      margin-bottom: 20px;
    }
    .search-form input {
      padding: 10px;
      width: 300px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 15px;
    }
    details {
      margin-top: 20px;
      background: #fff;
      border-radius: 8px;
      border: 1px solid #ddd;
      overflow: hidden;
    }
    summary {
      padding: 14px 20px;
      font-weight: bold;
      background: #fef2f2;
      cursor: pointer;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 10px 0;
    }
    th, td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #eee;
    }
    th {
      background-color: var(--primary);
      color: white;
      position: sticky;
      top: 0;
    }
    button {
      background-color: var(--primary);
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      margin: 2px;
    }
    .btn-outline {
      background: transparent;
      color: var(--primary);
      border: 1px solid var(--primary);
    }
    .btn-outline:hover {
      background: var(--primary);
      color: white;
    }
    .actions form {
      display: inline-block;
    }
    .back-button {
      text-align: right;
      margin-bottom: 10px;
    }
    .back-link {
      background-color: var(--primary);
      color: white;
      padding: 10px 18px;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
    }
    .navbar {
      background: var(--primary);
      color: white;
      padding: 14px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 1000;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    .navbar h1 {
      margin: 0;
      font-size: 22px;
    }
    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      margin-left: 15px;
    }
    .status {
      padding: 5px 10px;
      border-radius: 20px;
      font-weight: bold;
      color: white;
      font-size: 13px;
    }
    .status.Pending { background: #6c757d; }
    .status.Preparing { background: #fd7e14; }
    .status.Out_for_Delivery { background: #007bff; }
    .status.Completed { background: #28a745; }
    .status.Cancelled { background: #dc3545; }
  </style>
</head>
<body>

<div class="navbar">
  <h1>Bella Pizza</h1>
  <div class="nav-links">
    <a href="dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
  </div>
</div>

<div class="container">
  <div class="back-button">
    <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
  </div>

  <h2>Manage Orders</h2>

  <div class="search-form">
    <input type="text" id="searchInput" placeholder="Search by customer or order ID">
  </div>

  <div id="ordersContainer">
    <?php renderSections($today_orders, $past_orders, $role); ?>
  </div>
</div>

<script>
  $('#searchInput').on('input', function () {
    const val = $(this).val().toLowerCase();
    $('table tr').each(function (i) {
      if (i === 0) return;
      const row = $(this);
      const id = row.find('td:first').text().toLowerCase();
      const name = row.find('.searchable').text().toLowerCase();
      row.toggle(id.includes(val) || name.includes(val));
    });
  });

  $(document).on('submit', '.status-form', function (e) {
    e.preventDefault();
    const form = $(this);
    $.post('', form.serialize(), function () {
      fetchOrders();
    });
  });

  function fetchOrders() {
    $.get(window.location.href.split('?')[0] + '?ajax=1', function (data) {
      $('#ordersContainer').html(data);
    });
  }

  setInterval(fetchOrders, 10000);
</script>

</body>
</html>
