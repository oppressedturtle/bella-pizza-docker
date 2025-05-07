<?php
session_start();
require_once "log_helper.php"; // make sure this is available and correct

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

if (!isset($_SESSION["employee_id"]) || !in_array($_SESSION["role"], ['admin', 'support'])) {
    header("Location: login.php");
    exit();
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    if ($name !== "") {
        $stmt = $pdo->prepare("INSERT INTO category (name) VALUES (?)");
        $stmt->execute([$name]);
        $message = "✅ Category added successfully!";
        log_action("Category Added", "New category '$name' was added.", "INFO", $_SESSION["employee_id"], null, $_SESSION["username"]);
    } else {
        $message = "⚠️ Please enter a category name.";
        log_action("Category Add Failed", "Attempted to add category with empty name.", "WARNING", $_SESSION["employee_id"], null, $_SESSION["username"]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add Category | Bella Pizza</title>
  <!-- Styles remain unchanged -->
  <style>
    :root {
      --primary: #dc3545;
      --light: #fff;
      --bg: #f4f4f4;
    }
    * { box-sizing: border-box; margin:0; padding:0; }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: var(--bg);
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      color: #333;
    }
    .header {
      background: var(--primary);
      color: var(--light);
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
    }
    .header .logo { font-size: 1.5rem; font-weight: bold; }
    .header nav button,
    .header nav a.logout {
      background: transparent;
      border: none;
      color: var(--light);
      font-weight: bold;
      margin-left: 15px;
      cursor: pointer;
      text-decoration: none;
      font-size: 1rem;
    }
    .header nav button:hover,
    .header nav a.logout:hover { text-decoration: underline; }
    main {
      flex: 1;
      padding: 20px;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .card {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      padding: 20px;
      width: 90%;
      max-width: 500px;
    }
    .card h2 {
      margin-bottom: 15px;
      text-align: center;
      color: #333;
    }
    .message {
      text-align: center;
      margin-bottom: 15px;
      font-weight: bold;
      color: green;
    }
    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
    }
    input[type="text"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 1rem;
    }
    button.submit {
      width: 100%;
      background: var(--primary);
      color: var(--light);
      border: none;
      padding: 12px;
      border-radius: 6px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    button.submit:hover { background: #a71d2a; }
  </style>
</head>
<body>
  <header class="header">
    <div class="logo">Bella Pizza 🍕</div>
    <nav>
      <button onclick="location.href='edit_menu_items.php'">← Back to Menu</button>
      <a href="logout.php" class="logout">Logout</a>
    </nav>
  </header>

  <main>
    <div class="card">
      <h2>Add New Category</h2>
      <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>
      <form method="POST">
        <label for="name">Category Name</label>
        <input type="text" id="name" name="name" required>
        <button type="submit" class="submit">➕ Add Category</button>
      </form>
    </div>
  </main>
</body>
</html>
