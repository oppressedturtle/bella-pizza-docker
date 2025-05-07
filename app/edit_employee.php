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

if (!isset($_GET['id'])) {
    echo "No employee selected."; exit();
}

$employee_id = intval($_GET['id']);
$error = $success = "";


if (isset($_GET['check'])) {
    $value = $_GET['check'];
    $stmt = $conn->prepare("SELECT 1 FROM employee WHERE (username = ? OR email = ?) AND employee_id != ?");
    $stmt->bind_param("ssi", $value, $value, $employee_id);
    $stmt->execute();
    $stmt->store_result();
    echo $stmt->num_rows > 0 ? 'taken' : 'available';
    $stmt->close();
    exit;
}


$stmt = $conn->prepare("SELECT * FROM employee WHERE employee_id = ?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    echo "Employee not found."; exit();
}
$employee = $result->fetch_assoc();
$stmt->close();


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validate_csrf_token();

    $first_name = $_POST["first_name"];
    $last_name  = $_POST["last_name"];
    $username   = $_POST["username"];
    $email      = $_POST["email"];
    $phone      = $_POST["phone"];
    $role       = $_POST["role"];
    $salary     = floatval($_POST["salary"]);

    $update = $conn->prepare("UPDATE employee SET first_name = ?, last_name = ?, username = ?, email = ?, phone_number = ?, role = ?, salary = ? WHERE employee_id = ?");
    $update->bind_param("ssssssdi", $first_name, $last_name, $username, $email, $phone, $role, $salary, $employee_id);

    if ($update->execute()) {
        $success = "Employee updated successfully.";

        log_action(
            "Update Employee",
            "Updated employee #$employee_id ($username)",
            "INFO",
            $_SESSION["employee_id"],
            null,
            $_SESSION["username"]
        );

        $stmt = $conn->prepare("SELECT * FROM employee WHERE employee_id = ?");
        $stmt->bind_param("i", $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $employee = $result->fetch_assoc();
        $stmt->close();
    } else {
        $error = "Update failed.";
    }
    $update->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Employee | Bella Pizza</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #dc3545;
      --accent: #f8c102;
      --bg-light: #fff;
      --text-dark: #333;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Fredoka', sans-serif;
      background-color: var(--bg-light);
      color: var(--text-dark);
    }
    .navbar {
      background-color: var(--primary);
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 14px 24px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      position: sticky;
      top: 0;
      z-index: 1000;
    }
    .navbar h1 {
      margin: 0;
      font-size: 22px;
    }
    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: bold;
      margin-left: 15px;
    }
    .nav-links a:hover {
      color: var(--accent);
    }
    .container {
      max-width: 700px;
      background-color: var(--bg-light);
      margin: 80px auto 30px;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 25px;
    }
    label {
      display: block;
      margin-bottom: 10px;
      font-weight: 500;
    }
    input, select {
      width: 100%;
      padding: 12px;
      margin-top: 5px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
    }
    #username-status, #email-status {
      font-size: 0.9rem;
      margin-top: -16px;
      margin-bottom: 15px;
      padding-left: 5px;
    }
    #username-status.taken, #email-status.taken { color: red; }
    #username-status.available, #email-status.available { color: green; }

    button {
      width: 100%;
      background-color: var(--primary);
      color: white;
      border: none;
      padding: 14px;
      border-radius: 8px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }
    button:hover {
      background-color: #a71d2a;
    }
    .success, .error {
      text-align: center;
      font-weight: bold;
      margin-bottom: 20px;
    }
    .success { color: green; }
    .error   { color: red; }
    .back-link {
      text-align: center;
      margin-top: 20px;
    }
    .back-link a {
      text-decoration: none;
      background-color: var(--accent);
      color: black;
      padding: 10px 18px;
      border-radius: 6px;
      font-weight: bold;
      display: inline-block;
    }
    .back-link a:hover {
      background-color: #ddb400;
    }
    @media (max-width: 768px) {
      .container { margin: 20px 10px; padding: 20px; }
      .navbar { flex-direction: column; align-items: flex-start; }
      .nav-links { margin-top: 10px; }
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
  <h2>Edit Employee</h2>

  <?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
  <?php elseif ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <label>First Name:
      <input type="text" name="first_name" value="<?= htmlspecialchars($employee['first_name']) ?>" required>
    </label>

    <label>Last Name:
      <input type="text" name="last_name" value="<?= htmlspecialchars($employee['last_name']) ?>" required>
    </label>

    <label>Username:
      <input type="text" name="username" id="username" value="<?= htmlspecialchars($employee['username']) ?>" required>
    </label>
    <div id="username-status"></div>

    <label>Email:
      <input type="email" name="email" id="email" value="<?= htmlspecialchars($employee['email']) ?>" required>
    </label>
    <div id="email-status"></div>

    <label>Phone Number:
      <input type="text" name="phone" value="<?= htmlspecialchars($employee['phone_number']) ?>">
    </label>

    <label>Role:
      <select name="role" required>
        <?php foreach (['admin', 'chef', 'cashier', 'delivery', 'support', 'janitor'] as $roleOption): ?>
          <option value="<?= $roleOption ?>" <?= $employee['role'] === $roleOption ? 'selected' : '' ?>>
            <?= ucfirst($roleOption) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>Salary:
      <input type="number" name="salary" step="0.01" value="<?= htmlspecialchars($employee['salary']) ?>" required>
    </label>

    <button type="submit">Update Employee</button>
  </form>

  <div class="back-link">
    <a href="employees.php">← Back to Employees</a>
  </div>
</div>

<script>
  function checkAvailability(inputId, statusId) {
    const val = document.getElementById(inputId).value;
    const id = <?= $employee_id ?>;
    if (val.length < 3) {
      document.getElementById(statusId).textContent = '';
      return;
    }
    fetch(`edit_employee.php?check=${encodeURIComponent(val)}&id=${id}`)
      .then(res => res.text())
      .then(data => {
        const el = document.getElementById(statusId);
        if (data === 'taken') {
          el.textContent = `${inputId.charAt(0).toUpperCase() + inputId.slice(1)} already in use`;
          el.className = 'taken';
        } else {
          el.textContent = 'Available';
          el.className = 'available';
        }
      });
  }

  document.getElementById("username").addEventListener("input", () => checkAvailability("username", "username-status"));
  document.getElementById("email").addEventListener("input", () => checkAvailability("email", "email-status"));
</script>

</body>
</html>
