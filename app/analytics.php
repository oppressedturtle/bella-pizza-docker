<?php
require_once 'includes/session_config.php';
require_once 'includes/csrf.php';

if (!isset($_SESSION["employee_id"]) || $_SESSION["role"] !== 'admin') {
    header("Location: login.php");
    exit();
}

$pdo = new PDO("mysql:host=db;dbname=RestaurantDB", "root", "rootpass");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);




$startDate = $_GET['start'] ?? date('Y-m-01');
$endDate   = $_GET['end'] ?? date('Y-m-d');

$params = [':start' => $startDate, ':end' => $endDate];
$condition = "WHERE DATE(order_date) BETWEEN :start AND :end";
$nonCancelledCondition = $condition . " AND status != 'cancelled'";



$totalOrders = $pdo->prepare("SELECT COUNT(*) FROM `order` $condition");
$totalOrders->execute($params);
$totalOrders = $totalOrders->fetchColumn();


$totalRevenue = $pdo->prepare("SELECT SUM(total_amount) FROM `order` $nonCancelledCondition");
$totalRevenue->execute($params);
$totalRevenue = $totalRevenue->fetchColumn() ?: 0;


$orderStatuses = $pdo->prepare("
  SELECT status, COUNT(*) AS c, SUM(total_amount) AS total
  FROM `order`
  $condition
  GROUP BY status
");
$orderStatuses->execute($params);
$orderStatuses = $orderStatuses->fetchAll(PDO::FETCH_ASSOC);


$allItems = $pdo->prepare("
  SELECT m.item_name, SUM(oi.quantity) AS total_sold, SUM(oi.quantity * m.price) AS total_amount
  FROM order_items oi
  JOIN menu m ON oi.menu_id = m.menu_id
  JOIN `order` o ON o.order_id = oi.order_id
  $nonCancelledCondition
  GROUP BY oi.menu_id
  ORDER BY total_sold DESC
");
$allItems->execute($params);
$allItems = $allItems->fetchAll(PDO::FETCH_ASSOC);


$dailyRevenue = $pdo->prepare("
  SELECT DATE(order_date) AS day, SUM(total_amount) AS revenue
  FROM `order`
  $nonCancelledCondition
  GROUP BY day ORDER BY day
");
$dailyRevenue->execute($params);
$dailyDates = [];
$dailyValues = [];
foreach ($dailyRevenue as $row) {
  $dailyDates[] = $row['day'];
  $dailyValues[] = round($row['revenue'], 2);
}


$statusLabels = array_column($orderStatuses, 'status');
$statusCounts = array_column($orderStatuses, 'c');
$itemLabels   = array_column($allItems, 'item_name');
$itemCounts   = array_column($allItems, 'total_sold');


$loginQuery = $pdo->prepare("
  SELECT COUNT(*) FROM customer_logins
  WHERE DATE(login_time) BETWEEN :start AND :end
");
$loginQuery->execute($params);
$totalCustomerLogins = $loginQuery->fetchColumn();


$newCustomersQuery = $pdo->prepare("
  SELECT COUNT(*) FROM (
    SELECT customer_id, MIN(login_time) AS first_login
    FROM customer_logins
    GROUP BY customer_id
    HAVING DATE(first_login) BETWEEN :start AND :end
  ) AS new_customers
");
$newCustomersQuery->execute($params);
$newCustomers = $newCustomersQuery->fetchColumn();


$returningCustomersQuery = $pdo->prepare("
  SELECT COUNT(DISTINCT current.customer_id) FROM customer_logins current
  JOIN (
    SELECT customer_id
    FROM customer_logins
    WHERE DATE(login_time) < :start
  ) AS previous ON current.customer_id = previous.customer_id
  WHERE DATE(current.login_time) BETWEEN :start AND :end
");
$returningCustomersQuery->execute($params);
$returningCustomers = $returningCustomersQuery->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Analytics | Bella Pizza</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
      --primary: #dc3545;
      --accent: #f8c102;
      --bg: #fff8f3;
      --light: #fff;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Fredoka', sans-serif;
      background: var(--bg);
      padding: 30px;
      color: #333;
    }
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: var(--primary);
      padding: 15px 25px;
      border-radius: 10px;
      color: white;
      margin-bottom: 20px;
    }
    .navbar h1 {
      font-size: 1.5rem;
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
    .filters, .cards, .charts, .tables {
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      margin-bottom: 25px;
    }
    .filters form {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }
    .filters label {
      font-weight: bold;
    }
    .filters input, .filters select {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .filters button {
      background: var(--primary);
      color: white;
      padding: 10px 20px;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s;
    }
    .filters button:hover {
      background: #a71d2a;
    }
    .cards {
      display: flex;
      gap: 20px;
      justify-content: space-around;
      flex-wrap: wrap;
    }
    .card {
      background: var(--light);
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.08);
      flex: 1;
      min-width: 220px;
    }
    .card h3 {
      margin-bottom: 10px;
      color: var(--primary);
    }
    .tables table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    .tables th, .tables td {
      border: 1px solid #eee;
      padding: 10px;
      text-align: center;
    }
    .tables th {
      background: var(--primary);
      color: white;
    }
    .charts .side-by-side {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
    }
    .charts canvas {
      display: block;
      margin: 20px auto;
    }
    .toggle-refresh {
      margin-top: 10px;
      text-align: center;
    }
    .toggle-refresh input {
      transform: scale(1.2);
    }
    .download-btn {
      text-align: center;
      margin-top: 10px;
    }
    .download-btn button {
      background: var(--accent);
      color: #000;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      padding: 10px 20px;
      cursor: pointer;
    }
  </style>
</head>
<body>

<div class="navbar">
  <h1>Bella Pizza</h1>
  <div class="actions">
    <button onclick="location.href='dashboard.php'">Dashboard</button>
    <button onclick="location.href='logout.php'">Logout</button>
  </div>
</div>

<div class="filters">
  <form method="get">
    <label>Start Date:</label>
    <input type="date" name="start" value="<?= $startDate ?>">
    <label>End Date:</label>
    <input type="date" name="end" value="<?= $endDate ?>">
    <button type="submit">🔍 Filter</button>
  </form>
  <div class="toggle-refresh">
    <label><input type="checkbox" id="autoRefresh"> Auto-refresh every 30s</label>
  </div>
  <div class="download-btn">
    <button onclick="downloadCSV()">📥 Download CSV</button>
  </div>
</div>

<div class="cards">
<div class="card">
  <h3>Total Unique Visits</h3>
  <p><?= $newCustomers ?></p>
</div>
<div class="card">
  <h3>Total Customer Logins</h3>
  <p><?= $totalCustomerLogins ?></p>
</div>
<div class="card">
  <h3>New Customers</h3>
  <p><?= $newCustomers ?></p>
</div>
<div class="card">
  <h3>Returning Customers</h3>
  <p><?= $returningCustomers ?></p>
</div>
  <div class="card">
    <h3>Total Orders</h3>
    <p><?= $totalOrders ?></p>
  </div>
  <div class="card">
    <h3>Total Revenue</h3>
    <p><?= number_format($totalRevenue, 2) ?> BD</p>
  </div>
</div>

<div class="tables">
  <h2>Orders by Status</h2>
  <table>
    <tr><th>Status</th><th>Count</th><th>Total Amount</th></tr>
    <?php foreach ($orderStatuses as $s): ?>
      <tr>
        <td><?= htmlspecialchars($s['status']) ?></td>
        <td><?= $s['c'] ?></td>
        <td><?= number_format($s['total'], 2) ?> BD</td>
      </tr>
    <?php endforeach; ?>
  </table>

  <h2>All Menu Items Sold</h2>
  <table>
    <tr><th>Item Name</th><th>Total Sold</th><th>Total Amount</th></tr>
    <?php foreach ($allItems as $i): ?>
      <tr>
        <td><?= htmlspecialchars($i['item_name']) ?></td>
        <td><?= $i['total_sold'] ?></td>
        <td><?= number_format($i['total_amount'], 2) ?> BD</td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<div class="charts">
  <div class="side-by-side">
    <canvas id="itemsChart" width="400" height="400"></canvas>
  </div>
  <canvas id="revenueChart" width="1000" height="400"></canvas>
</div>
<?php
$csvRows = [
  ['Bella Pizza Analytics Report'],
  ['Date Range:', "$startDate to $endDate"],
  ['Generated At:', date("Y-m-d H:i:s")],
  [],
  ['Total Orders', $totalOrders],
  ['Total Revenue', number_format($totalRevenue, 2) . ' BD'],
  ['Total Visits', $totalVisits],
  ['Total Unique Visits', $totalUniqueVisits],
  ['Total Customer Logins', $totalCustomerLogins],
  ['New Customers', $newCustomers],
  ['Returning Customers', $returningCustomers],
  [],
  ['Status', 'Count', 'Total Amount']
];

foreach ($orderStatuses as $s) {
  $csvRows[] = [
    $s['status'],
    $s['c'],
    number_format($s['total'], 2) . ' BD'
  ];
}

$csvRows[] = [];
$csvRows[] = ['Item Name', 'Total Sold', 'Total Amount'];

foreach ($allItems as $i) {
  $csvRows[] = [
    $i['item_name'],
    $i['total_sold'],
    number_format($i['total_amount'], 2) . ' BD'
  ];
}
?>

<script>
  Chart.defaults.font.family = 'Fredoka, sans-serif';
  Chart.defaults.font.size = 14;
  Chart.defaults.color = '#333';

  new Chart(document.getElementById('itemsChart'), {
    type: 'bar',
    data: {
      labels: <?= json_encode($itemLabels) ?>,
      datasets: [{
        label: 'Items Sold',
        data: <?= json_encode($itemCounts) ?>,
        backgroundColor: '#dc3545'
      }]
    },
    options: { scales: { y: { beginAtZero: true } } }
  });

  new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
      labels: <?= json_encode($dailyDates) ?>,
      datasets: [{
        label: 'Revenue Over Time',
        data: <?= json_encode($dailyValues) ?>,
        borderColor: '#28a745',
        fill: false
      }]
    }
  });

  let interval;
  document.getElementById('autoRefresh').addEventListener('change', function () {
    if (this.checked) {
      interval = setInterval(() => location.reload(), 30000);
    } else {
      clearInterval(interval);
    }
  });

  function downloadCSV() {
    const rows = <?= json_encode($csvRows) ?>;
    const csv = rows.map(r => r.join(",")).join("\n");

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `analytics_report_<?= $startDate ?>_to_<?= $endDate ?>.csv`;
    link.click();
  }
</script>




</body>
</html>
