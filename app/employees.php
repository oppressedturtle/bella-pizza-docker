<?php
require_once 'includes/session_config.php';
require_once 'includes/csrf.php';

$conn = new mysqli("db", "root", "rootpass", "RestaurantDB");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION["employee_id"]) || !in_array($_SESSION["role"], ['admin', 'support'])) {
    header("Location: login.php");
    exit();
}

$employees = [];
$stmt = $conn->prepare("SELECT * FROM employee WHERE is_deleted = 0 ORDER BY employee_id DESC");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Employees | Bella Pizza</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    .navbar {
      position: sticky;
      top: 0;
      background: var(--primary);
      color: white;
      padding: 14px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 1000;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }

    .navbar h1 { margin: 0; font-size: 22px; }

    .nav-links {
      display: flex;
      gap: 15px;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      padding: 8px 12px;
      border-radius: 6px;
    }

    .nav-links a:hover { background: rgba(255,255,255,0.15); }

    .container {
      background: var(--light);
      padding: 30px;
      border-radius: 12px;
      max-width: 1200px;
      margin: 40px auto;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: var(--primary);
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }

    .header-buttons {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .btn {
      padding: 10px 16px;
      border-radius: 6px;
      font-weight: bold;
      text-decoration: none;
      color: white;
      display: inline-block;
    }

    .btn.primary { background: var(--primary); }
    .btn.primary:hover { background: #a71d2a; }

    .btn.accent { background: var(--accent); color: black; }
    .btn.accent:hover { background: #ddb400; }

    .search-box input {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      width: 250px;
      font-size: 14px;
    }

    .summary {
      font-size: 14px;
      color: #555;
      text-align: right;
      margin-top: 10px;
      width: 100%;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px;
      border-bottom: 1px solid #ddd;
      text-align: center;
    }

    th {
      background-color: var(--primary);
      color: var(--light);
      position: sticky;
      top: 0;
    }

    .badge {
      padding: 4px 8px;
      border-radius: 6px;
      color: white;
      font-size: 13px;
    }

    .admin { background-color: #6f42c1; }
    .chef { background-color: #20c997; }
    .cashier { background-color: #17a2b8; }
    .support { background-color: #fd7e14; }
    .delivery { background-color: #28a745; }
    .janitor { background-color: #343a40; }

    .action-buttons a {
      padding: 6px 12px;
      font-weight: bold;
      font-size: 14px;
      border-radius: 5px;
      text-decoration: none;
      color: var(--light);
    }

    .edit-btn { background: var(--accent); }
    .edit-btn:hover { background: #c79100; }

    .delete-btn { background: var(--primary); }
    .delete-btn:hover { background: #a71d2a; }
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
  <div class="top-bar">
    <div class="header-buttons">
      <a href="add_employee.php" class="btn primary">+ Add Employee</a>
      <a href="#" id="exportCsv" class="btn accent">Export CSV</a>
    </div>
    <div class="search-box">
      <input type="text" id="searchInput" placeholder="Search employees...">
    </div>
    <div class="summary">Total Employees: <span id="totalCount"></span></div>
  </div>

  <div class="table-container">
    <table id="employeeTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>Full Name</th>
          <th>Username</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Role</th>
          <th>Salary</th>
          <th>Hire Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($employees as $row): ?>
          <tr>
            <td><?= $row['employee_id'] ?></td>
            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone_number']) ?></td>
            <td><span class="badge <?= htmlspecialchars(strtolower($row['role'])) ?>"><?= htmlspecialchars(ucfirst($row['role'])) ?></span></td>
            <td>BD <?= number_format($row['salary'], 2) ?></td>
            <td><?= $row['hire_date'] ?></td>
            <td class="action-buttons">
              <a href="edit_employee.php?id=<?= $row['employee_id'] ?>" class="edit-btn">Update</a>
              <a href="#" class="delete-btn" data-id="<?= $row['employee_id'] ?>" data-token="<?= $_SESSION['csrf_token'] ?>">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
$(document).ready(function () {
  $('#totalCount').text($('#employeeTable tbody tr').length);

  $('#searchInput').on('input', function () {
    const val = $(this).val().toLowerCase();
    let count = 0;
    $('#employeeTable tbody tr').each(function () {
      const rowText = $(this).text().toLowerCase();
      const match = rowText.includes(val);
      $(this).toggle(match);
      if (match) count++;
    });
    $('#totalCount').text(count);
  });

  $(document).on('click', '.delete-btn', function (e) {
    e.preventDefault();
    if (confirm('Are you sure you want to deactivate this employee?')) {
      const id = $(this).data('id');
      const token = $(this).data('token');
      $.post('soft_delete_employee.php', { id: id, csrf_token: token }, function (res) {
        location.reload();
      });
    }
  });

  $('#exportCsv').on('click', function () {
    let csv = "ID,Full Name,Username,Email,Phone,Role,Salary,Hire Date\n";
    $('#employeeTable tbody tr:visible').each(function () {
      const cols = $(this).find('td');
      csv += `"${cols.eq(0).text()}","${cols.eq(1).text()}","${cols.eq(2).text()}","${cols.eq(3).text()}","${cols.eq(4).text()}","${cols.eq(5).text()}","${cols.eq(6).text()}","${cols.eq(7).text()}"\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'employees.csv';
    a.click();
    URL.revokeObjectURL(url);
  });
});
</script>

</body>
</html>
