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

if (!isset($_SESSION["employee_id"]) || !in_array($_SESSION["role"], ['admin', 'support'])) {
    header("Location: login.php");
    exit();
}

$message = '';

// Delete if requested
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM category WHERE category_id = ?");
    $stmt->execute([$_GET['delete']]);
    $message = "Category deleted successfully!";
}

// Fetch all categories
$categories = $pdo
    ->query("SELECT * FROM category ORDER BY category_id DESC")
    ->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Categories | Bella Pizza</title>
  <style>
    :root {
      --primary: #dc3545;
      --light: #fff;
      --bg: #f4f4f4;
      --card: #fff;
      --accent: #28a745;
    }
    * { box-sizing: border-box; margin:0; padding:0; }
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
      max-width: 800px;
      margin: 40px auto;
      background: var(--card);
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    .card {
      padding: 20px;
    }
    h2 {
      margin-bottom: 20px;
      color: var(--primary);
      text-align: center;
    }
    .message {
      text-align: center;
      margin-bottom: 12px;
      font-weight: bold;
      color: green;
    }
    .top-actions {
      display: flex;
      justify-content: center;
      gap: 12px;
      margin-bottom: 20px;
    }
    .top-actions a {
      background: var(--accent);
      color: var(--light);
      padding: 10px 16px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.2s;
    }
    .top-actions a:hover { background: #218838; }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #eee;
      text-align: left;
    }
    th {
      background: var(--primary);
      color: var(--light);
    }
    .actions a {
      margin-right: 8px;
      color: var(--primary);
      font-weight: bold;
      text-decoration: none;
    }
    .actions a:hover { text-decoration: underline; }
    @media (max-width: 600px) {
      .top-actions { flex-direction: column; }
      .top-actions a { width: 100%; text-align: center; }
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="logo">Bella Pizza 🍕</div>
    <nav>
      <button onclick="location.href='dashboard.php'">← Dashboard</button>
    </nav>
  </header>

  <div class="container">
    <div class="card">
      <h2>Manage Categories</h2>

      <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <div class="top-actions">
        <a href="add_category.php">➕ Add Category</a>
        <a href="dashboard.php">← Back to Dashboard</a>
      </div>

      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($categories as $cat): ?>
            <tr>
              <td><?= $cat['category_id'] ?></td>
              <td><?= htmlspecialchars($cat['name']) ?></td>
              <td class="actions">
                <a href="edit_category.php?id=<?= $cat['category_id'] ?>">Edit</a>
                <a
                  href="?delete=<?= $cat['category_id'] ?>"
                  onclick="return confirm('Are you sure you want to delete this category?')"
                >Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
