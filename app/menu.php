<?php
session_start();
$conn = new mysqli("db", "root", "rootpass", "RestaurantDB");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$category_sql = "SELECT * FROM category ORDER BY display_order ASC, name ASC";
$category_result = $conn->query($category_sql);
$categories = [];
while ($row = $category_result->fetch_assoc()) {
    $categories[] = $row;
}

$menu_items_by_category = [];
$menu_sql = "SELECT m.*, c.name AS category_name FROM menu m
             LEFT JOIN category c ON m.category_id = c.category_id
             WHERE m.availability = 1
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
  <title>Menu | Bella Pizza</title>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
        --primary: #dc3545;
        --accent: #f8c102;
        --light: #fff;
        --dark: #2d2d2d;
        --bg: #fff8f3;
    }
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    body {
        font-family: 'Fredoka', sans-serif;
        background: var(--bg);
        color: var(--dark);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    .header {
        background: var(--primary);
        color: var(--light);
        padding: 15px 25px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .logo {
        font-size: 1.8rem;
        font-weight: 600;
    }
    .logo span {
        color: var(--accent);
    }
    nav .home-btn {
        background: var(--accent);
        color: var(--dark);
        text-decoration: none;
        font-weight: 600;
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 1rem;
        transition: background 0.2s ease;
    }
    nav .home-btn:hover {
        background: #ffd43b;
    }
    .hero {
        background: url('img/banner.jpg') center/cover no-repeat;
        height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: yellow;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        font-size: 2rem;
        font-weight: bold;
    }
    main {
        flex: 1;
        padding: 30px 20px;
        max-width: 1200px;
        margin: auto;
    }
    section.category {
        margin-top: 40px;
    }
    section.category h2 {
        border-left: 10px solid var(--accent);
        padding-left: 10px;
        margin-bottom: 18px;
        font-size: 1.5rem;
    }
    .cards {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        padding-bottom: 10px;
        padding-left: 5px;
        scroll-padding: 10px;
    }
    .card {
        flex: 0 0 auto;
        min-width: 230px;
        max-width: 260px;
        background: var(--light);
        border-radius: 12px;
        box-shadow: 0 6px 10px rgba(0,0,0,0.08);
        display: flex;
        flex-direction: column;
        scroll-snap-align: start;
        transition: transform 0.2s ease;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .card img {
        width: 100%;
        height: 160px;
        object-fit: cover;
        background: #ffc;
    }
    .card-content {
        padding: 15px;
        flex: 1;
    }
    .card-content h3 {
        font-size: 1.2rem;
        margin-bottom: 8px;
    }
    .card-content p.desc {
        font-size: 0.9rem;
        margin-bottom: 10px;
        color: #555;
    }
    .card-content .price {
        font-weight: bold;
        color: var(--primary);
        font-size: 1.1rem;
    }
    @media (max-width: 600px) {
        .cards {
            grid-template-columns: 1fr;
        }
        .header {
            flex-direction: column;
            gap: 10px;
            text-align: center;
        }
        nav {
            flex-wrap: wrap;
            justify-content: center;
        }
    }
  </style>
</head>
<body>

<header class="header">
  <div class="logo">Bella <span>Pizza</span> </div>
  <nav>
    <a href="index.php" class="home-btn"> Home</a>
  </nav>
</header>

<div class="hero">Our Fresh Menu</div>

<main>
    <?php foreach ($categories as $cat): ?>
        <?php if (!empty($menu_items_by_category[$cat['category_id']])): ?>
            <section class="category">
                <h2><?= htmlspecialchars($cat['name']) ?></h2>
                <div class="cards">
                    <?php foreach ($menu_items_by_category[$cat['category_id']] as $item): ?>
                        <div class="card">
                            <img src="img/<?= htmlspecialchars($item['image_path'] ?: 'placeholder.png') ?>" alt="">
                            <div class="card-content">
                                <h3><?= htmlspecialchars($item['item_name']) ?></h3>
                                <p class="desc"><?= htmlspecialchars($item['description'] ?: 'No description.') ?></p>
                                <div class="price"><?= number_format($item['price'], 2) ?> BD</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>
</main>

</body>
</html>
