<?php
session_start();
$conn = new mysqli("db", "root", "rootpass", "RestaurantDB");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Load categories
$category_sql = "SELECT * FROM category ORDER BY display_order ASC, name ASC";
$category_result = $conn->query($category_sql);
$categories = [];
while ($row = $category_result->fetch_assoc()) {
    $categories[] = $row;
}

// Group menu items by category_id
$menu_items_by_category = [];
$menu_sql = "SELECT m.*, c.name AS category_name FROM menu m
             LEFT JOIN category c ON m.category_id = c.category_id
             ORDER BY m.display_order ASC";
$menu_result = $conn->query($menu_sql);
while ($row = $menu_result->fetch_assoc()) {
    $menu_items_by_category[$row['category_id']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Menu | Bella Pizza</title>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #dc3545;
      --accent: #f8c102;
      --bg: #fff8f3;
      --light: #fff;
    }
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Fredoka', sans-serif;
      background: var(--bg);
      color: #333;
      padding: 30px 20px;
    }
    .container {
      max-width: 1000px;
      margin: 0 auto;
      background: var(--light);
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.08);
    }
    h1 {
      text-align: center;
      margin-bottom: 30px;
      color: var(--primary);
    }
    .nav {
      text-align: center;
      margin-bottom: 25px;
    }
    .nav a {
      background-color: var(--primary);
      color: white;
      text-decoration: none;
      padding: 10px 16px;
      border-radius: 6px;
      margin: 0 10px;
      font-weight: bold;
      display: inline-block;
    }
    .nav a:hover {
      background-color: #a71d2a;
    }
    .category {
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
      overflow: hidden;
      background: #fff;
    }
    .category-header {
      padding: 16px 20px;
      background: #fef2f2;
      font-weight: bold;
      font-size: 18px;
      cursor: pointer;
    }
    .category-items {
      display: none;
      padding: 10px 20px 20px;
    }
    .menu-item {
      display: flex;
      gap: 20px;
      padding: 16px 0;
      border-bottom: 1px solid #eee;
      align-items: center;
    }
    .menu-item:last-child {
      border-bottom: none;
    }
    .menu-item img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 10px;
      flex-shrink: 0;
    }
    .menu-details {
      flex: 1;
    }
    .menu-details h3 {
      margin: 0 0 8px;
      font-size: 18px;
      color: var(--primary);
    }
    .menu-details p {
      margin: 4px 0;
      font-size: 15px;
      color: #555;
    }
    .no-image {
      width: 120px;
      height: 120px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-style: italic;
      color: #aaa;
      background: #f5f5f5;
      border-radius: 10px;
    }
    @media (max-width: 700px) {
      .menu-item {
        flex-direction: column;
        align-items: flex-start;
      }
      .menu-item img, .no-image {
        width: 100%;
        height: auto;
      }
    }
  </style>
</head>
<body>

<div class="container">
  <div class="nav">
    <a href="index.php">🏠 Home</a>
    <a href="login.php">🔐 Login</a>
  </div>

  <h1>Our Menu</h1>

  <?php foreach ($categories as $cat): ?>
    <?php if (!empty($menu_items_by_category[$cat['category_id']])): ?>
      <div class="category">
        <div class="category-header"><?= htmlspecialchars($cat['name']) ?></div>
        <div class="category-items">
          <?php foreach ($menu_items_by_category[$cat['category_id']] as $item): ?>
            <div class="menu-item">
              <?php if (!empty($item['image_path'])): ?>
                <img src="img/<?= htmlspecialchars($item['image_path']) ?>" alt="Item Image">
              <?php else: ?>
                <div class="no-image">No image</div>
              <?php endif; ?>
              <div class="menu-details">
                <h3><?= htmlspecialchars($item['item_name']) ?></h3>
                <p><?= htmlspecialchars($item['description']) ?></p>
                <p><strong>Price:</strong> <?= number_format($item['price'], 2) ?> BD</p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
</div>

<script>
  document.querySelectorAll('.category-header').forEach(header => {
    header.addEventListener('click', () => {
      const items = header.nextElementSibling;
      items.style.display = items.style.display === 'block' ? 'none' : 'block';
    });
  });
</script>

</body>
</html>
