<?php
require_once __DIR__ . '/includes/session_config.php';
require_once __DIR__ . '/includes/csrf.php';

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


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['toggle_id'])) {
    validate_csrf_token();
    $id = (int) $_POST['toggle_id'];
    $available = $_POST['available'] === '1' ? 1 : 0;
    $stmt = $pdo->prepare("UPDATE menu SET availability = ? WHERE menu_id = ?");
    $stmt->execute([$available, $id]);

    log_action("Item Availability Toggled", "Menu item ID $id marked as " . ($available ? "available" : "unavailable"), "INFO", $_SESSION['employee_id'], null, $_SESSION['username']);
    exit;
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['order']) && is_array($_POST['order'])) {
    validate_csrf_token();
    foreach ($_POST['order'] as $index => $menu_id) {
        $stmt = $pdo->prepare("UPDATE menu SET display_order = ? WHERE menu_id = ?");
        $stmt->execute([$index, (int)$menu_id]);
    }
    log_action("Menu Order Changed", "Admin reordered menu items", "INFO", $_SESSION['employee_id'], null, $_SESSION['username']);
    exit;
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['category_order']) && is_array($_POST['category_order'])) {
    validate_csrf_token();
    foreach ($_POST['category_order'] as $index => $cat_id) {
        $stmt = $pdo->prepare("UPDATE category SET display_order = ? WHERE category_id = ?");
        $stmt->execute([$index, (int)$cat_id]);
    }
    log_action("Category Order Changed", "Admin reordered menu categories", "INFO", $_SESSION['employee_id'], null, $_SESSION['username']);
    exit;
}


$category_stmt = $pdo->query("SELECT * FROM category ORDER BY display_order ASC, name ASC");
$categories = $category_stmt->fetchAll(PDO::FETCH_ASSOC);
$menu_by_category = [];

foreach ($categories as $cat) {
    $stmt = $pdo->prepare("SELECT * FROM menu WHERE category_id = ? ORDER BY display_order ASC, item_name ASC");
    $stmt->execute([$cat['category_id']]);
    $menu_by_category[$cat['category_id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$csrf_token = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Menu Items | Bella Pizza</title>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
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
      color: #222;
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

    .navbar h1 {
      margin: 0;
      font-size: 22px;
      letter-spacing: 0.5px;
    }

    .nav-links {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      padding: 8px 12px;
      border-radius: 6px;
      transition: background 0.2s;
    }

    .nav-links a:hover {
      background: rgba(255,255,255,0.15);
    }

    .container {
      max-width: 1100px;
      background: var(--light);
      border-radius: 12px;
      padding: 30px;
      margin: 40px auto;
      box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }

    h2, h3 {
      text-align: center;
      color: var(--primary);
      margin-bottom: 20px;
    }

    .search-bar {
      margin-bottom: 25px;
      text-align: center;
    }

    .search-bar input {
      padding: 10px;
      width: 300px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 15px;
    }

    .category-group {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    details {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 8px;
      margin-bottom: 5px;
    }

    summary {
      padding: 14px 20px;
      background: #fef2f2;
      font-weight: bold;
      cursor: move;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #eee;
    }

    th {
      background-color: var(--primary);
      color: var(--light);
    }

    .actions a, .actions button {
      background: var(--primary);
      color: var(--light);
      border: none;
      padding: 6px 10px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
      margin: 2px;
      cursor: pointer;
    }

    .actions a:hover, .actions button:hover {
      background: #a71d2a;
    }

    .availability-toggle {
      cursor: pointer;
    }

    .bottom-links {
      text-align: center;
      margin-top: 30px;
    }

    .bottom-links button {
      background: var(--accent);
      color: black;
      border: none;
      padding: 10px 16px;
      border-radius: 6px;
      font-weight: bold;
      margin: 0 10px;
      cursor: pointer;
    }

    .bottom-links button:hover {
      background-color: #ddb400;
    }

    @media (max-width: 600px) {
      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }

      .nav-links {
        margin-top: 10px;
        width: 100%;
        flex-direction: column;
      }

      .search-bar input {
        width: 90%;
      }

      th, td {
        font-size: 14px;
        padding: 8px;
      }
      .drag-handle {
  cursor: grab;
  margin-right: 10px;
  font-weight: bold;
}

    }
    .nav-buttons {
  display: flex;
  gap: 10px;
}

.nav-btn {
  background: var(--accent);
  color: black;
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: bold;
  text-decoration: none;
  transition: background 0.3s ease;
}

.nav-btn:hover {
  background: #ddb400;
}

  </style>
</head>
<body>
<div class="navbar">
  <h1>Bella Pizza</h1>
  <div class="nav-buttons">
  <a href="dashboard.php" class="nav-btn">Dashboard</a>
  <a href="logout.php" class="nav-btn">Logout</a>
</div>

</div>

<div class="container">
  <h2>Edit Menu Items</h2>
  <div class="search-bar">
  <input type="text" id="searchInput" placeholder="Search menu items...">
</div>

  <div class="category-group" id="categoryGroup">
    <?php foreach ($categories as $cat): ?>
      <details data-cat-id="<?= $cat['category_id'] ?>">
        <summary><span class="drag-handle">☰</span> <?= htmlspecialchars($cat['name']) ?></summary>
        <table class="menu-table" data-category-id="<?= $cat['category_id'] ?>">
          <thead>
            <tr><th>ID</th><th>Image</th><th>Name</th><th>Description</th><th>Price</th><th>Available</th><th>Actions</th></tr>
          </thead>
          <tbody class="sortable">
            <?php foreach ($menu_by_category[$cat['category_id']] as $item): ?>
              <tr data-id="<?= $item['menu_id'] ?>">
                <td><?= $item['menu_id'] ?></td>
                <td>
                  <?php if (!empty($item['image_path'])): ?>
                    <img src="img/<?= htmlspecialchars($item['image_path']) ?>" style="width:60px; height:60px; border-radius:5px;">
                  <?php else: ?>
                    <span style="color:#aaa;">No image</span>
                  <?php endif; ?>
                </td>
                <td class="searchable"><?= htmlspecialchars($item['item_name']) ?></td>
                <td><?= htmlspecialchars($item['description']) ?></td>
                <td><?= number_format($item['price'], 2) ?> BD</td>
                <td>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <input type="hidden" name="toggle_id" value="<?= $item['menu_id'] ?>">
                    <input type="hidden" name="available" value="<?= $item['availability'] ? 0 : 1 ?>">
                    <input type="checkbox" onchange="this.form.submit()" <?= $item['availability'] ? 'checked' : '' ?>>
                  </form>
                </td>
                <td class="actions">
                  <a href="edit_menu_item.php?id=<?= $item['menu_id'] ?>">Edit</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </details>
    <?php endforeach; ?>
  </div>

  <div class="bottom-links">
    <form action="add_menu_item.php" method="get" style="display:inline;">
      <button type="submit">➕ Add Menu Item</button>
    </form>
    <form action="categories.php" method="get" style="display:inline;">
      <button type="submit">⚙️ Manage Categories</button>
    </form>
  </div>
</div>

<script>
  $('#searchInput').on('input', function () {
    const val = $(this).val().toLowerCase();
    $('tbody tr').each(function () {
      const text = $(this).find('.searchable').text().toLowerCase();
      $(this).toggle(text.includes(val));
    });
  });

  document.querySelectorAll('.sortable').forEach(tbody => {
    new Sortable(tbody, {
      animation: 150,
      onEnd: function () {
        const order = Array.from(tbody.children).map(row => row.dataset.id);
        $.post('', { order: order, csrf_token: '<?= $csrf_token ?>' });
      }
    });
  });

  new Sortable(document.getElementById('categoryGroup'), {
    animation: 150,
    handle: '.drag-handle',
    onEnd: function () {
      const catOrder = Array.from(document.querySelectorAll('#categoryGroup details')).map(d => d.dataset.catId);
      $.post('', { category_order: catOrder, csrf_token: '<?= $csrf_token ?>' });
    }
  });
</script>
</body>
</html>
