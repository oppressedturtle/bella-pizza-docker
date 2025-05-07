<?php
require_once 'includes/session_config.php';
require_once 'includes/csrf.php';
require_once 'log_helper.php';

$conn = new mysqli("db", "root", "rootpass", "RestaurantDB");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION["employee_id"]) || !in_array($_SESSION["role"], ['admin', 'support'])) {
    header("Location: login.php");
    exit();
}

$success = $error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validate_csrf_token();

    $first_name   = $_POST["first_name"];
    $last_name    = $_POST["last_name"];
    $username     = $_POST["username"];
    $email        = $_POST["email"];
    $phone_number = $_POST["phone"];
    $role         = $_POST["role"];
    $salary       = $_POST["salary"];
    $password     = $_POST["password"];
    $password_hash= password_hash($password, PASSWORD_BCRYPT);
    $hire_date    = date("Y-m-d");

    $checkStmt = $conn->prepare("SELECT 1 FROM employee WHERE username = ? OR email = ?");
    $checkStmt->bind_param("ss", $username, $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO employee (first_name, last_name, username, email, phone_number, role, salary, password_hash, hire_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssdss", $first_name, $last_name, $username, $email, $phone_number, $role, $salary, $password_hash, $hire_date);

        if ($stmt->execute()) {
            $success = "Employee added successfully.";
            log_action("Add Employee", "New employee '$username' added with role $role", "INFO", $_SESSION["employee_id"], null, $_SESSION["username"]);
        } else {
            $error = "Error: " . $stmt->error;
            log_action("Add Employee Failed", "DB error while adding employee: " . $stmt->error, "ERROR", $_SESSION["employee_id"], null, $_SESSION["username"]);
        }
        $stmt->close();
    } else {
        $error = "Username or email already exists.";
        log_action("Add Employee Failed", "Duplicate username/email for '$username'", "WARNING", $_SESSION["employee_id"], null, $_SESSION["username"]);
    }
    $checkStmt->close();
}


if (isset($_GET['check'])) {
    $value = $_GET['check'];
    $stmt = $conn->prepare("SELECT 1 FROM employee WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $value, $value);
    $stmt->execute();
    $stmt->store_result();
    echo $stmt->num_rows > 0 ? 'taken' : 'available';
    $stmt->close();
    exit;
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Employee | Bella Pizza</title>
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
      margin: 0;
      padding: 0;
    }

    .navbar {
      background: var(--primary);
      color: white;
      padding: 14px 24px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar h1 { margin: 0; font-size: 22px; }

    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      margin-left: 15px;
    }

    .container {
      background: var(--light);
      max-width: 550px;
      margin: 80px auto 30px;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: var(--primary);
    }

    .message {
      text-align: center;
      font-weight: bold;
      margin-bottom: 15px;
    }

    .success { color: green; }
    .error   { color: red; }

    label {
      display: block;
      margin-bottom: 10px;
      font-weight: 500;
    }

    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 1rem;
    }

    #username-status,
    #email-status {
      font-size: 0.9rem;
      margin-top: -18px;
      margin-bottom: 15px;
      padding-left: 5px;
    }

    #username-status.taken,
    #email-status.taken { color: red; }

    #username-status.available,
    #email-status.available { color: green; }

    button {
      width: 100%;
      padding: 12px;
      background: var(--primary);
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
    }

    button:hover {
      background: #a71d2a;
    }

    .back-link {
      text-align: center;
      margin-top: 20px;
    }

    .back-link a {
      background: #28a745;
      color: white;
      padding: 10px 16px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      display: inline-block;
    }

    .back-link a:hover {
      background: #1f8033;
    }

    @media (max-width: 400px) {
      input, select, button { font-size: 0.9rem; padding: 10px; }
    }
  </style>
</head>
<body>

<div class="navbar">
  <h1>Bella Pizza</h1>
  <div class="nav-links">
    <a href="dashboard.php">Dashboard</a>
    <a href="employees.php">Employees</a>
    <a href="logout.php">Logout</a>
  </div>
</div>

<div class="container">
  <h2>Add New Employee</h2>

  <?php if ($success): ?>
    <div class="message success"><?= htmlspecialchars($success) ?></div>
  <?php elseif ($error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <label>First Name
      <input type="text" name="first_name" required>
    </label>

    <label>Last Name
      <input type="text" name="last_name" required>
    </label>

    <label>Username
      <input type="text" name="username" id="username" required>
    </label>
    <div id="username-status"></div>

    <label>Email
      <input type="email" name="email" id="email" required>
    </label>
    <div id="email-status"></div>

    <label>Phone Number (optional)
      <input type="text" name="phone">
    </label>

    <label>Role
      <select name="role" required>
        <option value="">-- Select Role --</option>
        <option value="admin">Admin</option>
        <option value="chef">Chef</option>
        <option value="cashier">Cashier</option>
        <option value="delivery">Delivery</option>
        <option value="support">Support</option>
        <option value="janitor">Janitor</option>
      </select>
    </label>

    <label>Salary (BHD)
      <input type="number" name="salary" step="0.01" required>
    </label>

    <label>Password
      <input type="password" name="password" required>
    </label>

    <button type="submit">➕ Add Employee</button>
  </form>

  <div class="back-link">
    <a href="employees.php">← Back to Employee List</a>
  </div>
</div>

<script>
  function checkAvailability(inputId, statusId) {
    const val = document.getElementById(inputId).value;
    if (val.length < 3) {
      document.getElementById(statusId).textContent = '';
      return;
    }
    fetch(`add_employee.php?check=${encodeURIComponent(val)}`)
      .then(res => res.text())
      .then(data => {
        const status = document.getElementById(statusId);
        if (data === 'taken') {
          status.textContent = `${inputId.charAt(0).toUpperCase() + inputId.slice(1)} already in use`;
          status.className = 'taken';
        } else {
          status.textContent = 'Available';
          status.className = 'available';
        }
      });
  }

  document.getElementById("username").addEventListener("input", () => checkAvailability("username", "username-status"));
  document.getElementById("email").addEventListener("input", () => checkAvailability("email", "email-status"));
</script>

</body>
</html>
