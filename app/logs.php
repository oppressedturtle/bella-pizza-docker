<?php
require_once 'includes/session_config.php';
require_once 'includes/csrf.php';

$conn = new mysqli("db", "root", "rootpass", "RestaurantDB");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (!isset($_SESSION['employee_id']) || !in_array($_SESSION['role'], ['admin'])) {
    header("Location: login.php");
    exit();
}


$search = $_GET['search'] ?? '';
$level  = $_GET['level'] ?? '';
$start  = $_GET['start'] ?? '';
$end    = $_GET['end'] ?? '';
$page   = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit  = 20;
$offset = ($page - 1) * $limit;

$where = "WHERE 1";
$params = [];
$types = "";

if ($search !== '') {
    $where .= " AND (action LIKE ? OR username LIKE ? OR description LIKE ?)";
    $searchTerm = '%' . $search . '%';
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "sss";
}
if ($level !== '') {
    $where .= " AND log_level = ?";
    $params[] = $level;
    $types .= "s";
}
if ($start !== '' && $end !== '') {
    $where .= " AND DATE(log_time) BETWEEN ? AND ?";
    $params[] = $start;
    $params[] = $end;
    $types .= "ss";
}

$countSql = "SELECT COUNT(*) FROM logs $where";
$countStmt = $conn->prepare($countSql);
if ($types) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$countStmt->bind_result($total);
$countStmt->fetch();
$countStmt->close();

$dataSql = "SELECT * FROM logs $where ORDER BY log_time DESC LIMIT ? OFFSET ?";
$dataStmt = $conn->prepare($dataSql);
$params[] = $limit;
$params[] = $offset;
$types .= "ii";
$dataStmt->bind_param($types, ...$params);
$dataStmt->execute();
$logs = $dataStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>System Logs — Bella Pizza</title>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
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
      color: #333;
    }
    .navbar {
      position: sticky;
      top: 0;
      background: var(--primary);
      color: white;
      padding: 14px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
      z-index: 1000;
    }
    .navbar a { color: white; text-decoration: none; margin-left: 15px; font-weight: bold; }
    .navbar a:hover { color: var(--accent); }
    .container {
      max-width: 1200px;
      margin: 40px auto;
      background: var(--light);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }
    h2 { text-align: center; margin-bottom: 25px; color: var(--primary); }
    .filters {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 15px;
      margin-bottom: 25px;
    }
    .filters input, .filters select, .filters button {
      padding: 10px;
      font-size: 15px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    .filters button {
      background: var(--primary);
      color: white;
      font-weight: bold;
      cursor: pointer;
      border: none;
    }
    .table-wrapper {
      overflow-x: auto;
    }
    table {
      width: 100%;
      min-width: 1000px;
      border-collapse: collapse;
    }
    th, td {
      padding: 10px;
      border-bottom: 1px solid #eee;
      text-align: center;
    }
    th {
      background: var(--primary);
      color: white;
    }
    .pagination {
      text-align: center;
      margin-top: 20px;
    }
    .pagination a {
      margin: 0 4px;
      padding: 8px 12px;
      background: var(--primary);
      color: white;
      text-decoration: none;
      border-radius: 4px;
    }
    .pagination a.active {
      background: #a71d2a;
    }
  </style>
</head>
<body>
<div class="navbar">
  <div><strong>Bella Pizza</strong></div>
  <div>
    <a href="dashboard.php">Dashboard</a>
    <a href="logout.php">Logout</a>
  </div>
</div>

<div class="container">
  <h2>System Logs</h2>

  <form method="get" class="filters">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <input type="text" name="search" placeholder="Search logs..." value="<?= htmlspecialchars($search) ?>">
    <select name="level">
      <option value="">All Levels</option>
      <option value="INFO" <?= $level === 'INFO' ? 'selected' : '' ?>>INFO</option>
      <option value="WARNING" <?= $level === 'WARNING' ? 'selected' : '' ?>>WARNING</option>
      <option value="ERROR" <?= $level === 'ERROR' ? 'selected' : '' ?>>ERROR</option>
    </select>
    <input type="date" name="start" value="<?= htmlspecialchars($start) ?>">
    <input type="date" name="end" value="<?= htmlspecialchars($end) ?>">
    <button type="submit">Filter</button>
  </form>

  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Level</th>
          <th>Timestamp</th>
          <th>Employee ID</th>
          <th>Customer ID</th>
          <th>Username</th>
          <th>Action</th>
          <th>Description</th>
          <th>IP Address</th>
          <th>User Agent</th>
          <th>Session</th>
          <th>Location</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($log = $logs->fetch_assoc()): ?>
          <tr class="log-<?= strtolower($log['log_level']) ?>">
            <td><?= $log['log_id'] ?></td>
            <td><?= $log['log_level'] ?></td>
            <td><?= $log['log_time'] ?></td>
            <td><?= $log['employee_id'] ?? '-' ?></td>
            <td><?= $log['customer_id'] ?? '-' ?></td>
            <td><?= htmlspecialchars($log['username']) ?></td>
            <td><?= htmlspecialchars($log['action']) ?></td>
            <td><?= htmlspecialchars($log['description']) ?></td>
            <td><?= htmlspecialchars($log['ip_address']) ?></td>
            <td><?= htmlspecialchars($log['user_agent']) ?></td>
            <td><?= htmlspecialchars($log['session_id']) ?></td>
            <td><?= htmlspecialchars($log['location']) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <div class="pagination">
    <?php for ($i = 1; $i <= ceil($total / $limit); $i++): ?>
      <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
</div>
</body>
</html>
