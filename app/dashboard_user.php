<?php
require __DIR__ . '/includes/session_config.php';
require __DIR__ . '/includes/csrf.php';
include 'includes/chatbot.php'; 
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
$customerId = $_SESSION["user_id"];
$pdo = new PDO("mysql:host=db;dbname=RestaurantDB", "root", "rootpass");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$stmt = $pdo->prepare("INSERT INTO customer_logins (customer_id) VALUES (?)");
$stmt->execute([$customerId]);


$checkStmt = $pdo->prepare("SELECT COUNT(*) FROM customer_logins WHERE customer_id = ?");
$checkStmt->execute([$customerId]);
$loginCount = $checkStmt->fetchColumn();

$isFirstLogin = ($loginCount == 1);

$stmt = $pdo->prepare("SELECT username FROM customer WHERE customer_id = ?");
$stmt->execute([$_SESSION["user_id"]]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_to_cart"])) {
    validate_csrf_token();

    $menu_id  = (int)$_POST["menu_id"];
    $quantity = max(1, (int)($_POST["quantity"] ?? 1));

    if (!isset($_SESSION["cart"])) {
        $_SESSION["cart"] = [];
    }
    $_SESSION["cart"][$menu_id] = ($_SESSION["cart"][$menu_id] ?? 0) + $quantity;
    $message = "✅ Added to cart!";
}


$categories     = $pdo->query("SELECT * FROM category ORDER BY display_order ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC);
$category_items = [];
$itemStmt       = $pdo->prepare("SELECT * FROM menu WHERE availability=1 AND category_id=? ORDER BY item_name");
foreach ($categories as $cat) {
    $itemStmt->execute([$cat['category_id']]);
    $category_items[$cat['category_id']] = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bella Pizza Dashboard</title>
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
        nav {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        nav button,
        nav .logout-form button {
            background: var(--accent);
            color: var(--dark);
            border: none;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.2s ease;
        }
        nav button:hover,
        nav .logout-form button:hover {
            background: #ffd43b;
        }
        .cart-btn {
            position: relative;
        }
        .cart-btn::after {
            content: "<?= array_sum($_SESSION['cart'] ?? []) ?>";
            position: absolute;
            top: -6px;
            right: -8px;
            background: var(--light);
            color: var(--primary);
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
            font-weight: bold;
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
        .welcome {
            text-align: center;
            margin-bottom: 25px;
        }
        .message {
            background: #d4edda;
            color: #155724;
            padding: 12px 20px;
            margin-bottom: 20px;
            border-left: 6px solid green;
            font-weight: bold;
            animation: fadeInOut 3s ease forwards;
            text-align: center;
        }
        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateY(-10px); }
            10% { opacity: 1; transform: translateY(0); }
            90% { opacity: 1; }
            100% { opacity: 0; display: none; }
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
        .card-actions {
            padding: 12px 15px;
            border-top: 1px solid #eee;
            background: #fafafa;
        }
        .card-actions form {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
        }
        .card-actions input[type="number"] {
            width: 55px;
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .card-actions button {
            background: var(--primary);
            color: var(--light);
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            flex: 1;
            font-weight: bold;
        }
        .card-actions button:hover {
            background: #b2202d;
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
        <div class="logo">Bella <span>Pizza</span> 🍕</div>
        <nav>
            <button onclick="window.location='my_orders.php'">My Orders</button>
            <button onclick="window.location='view_cart.php'" class="cart-btn">Cart</button>
          
            <form method="POST" action="logout.php" class="logout-form">
                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <button name="logout">Logout</button>
            </form>
        </nav>
    </header>
    <div class="hero">Delicious, Fast & Fresh!</div>
    <main>
        <div class="welcome">
            <h2>Welcome, <?= htmlspecialchars($user['username']) ?>!</h2>
            <p>Order your favorite meals below.</p>
        </div>
        <?php if (!empty($message)): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <?php foreach ($categories as $cat): ?>
            <section class="category">
                <h2><?= htmlspecialchars($cat['name']) ?></h2>
                <div class="cards">
                    <?php foreach ($category_items[$cat['category_id']] as $item): ?>
                        <div class="card">
                            <img src="img/<?= htmlspecialchars($item['image_path'] ?: 'placeholder.png') ?>" alt="">
                            <div class="card-content">
                                <h3><?= htmlspecialchars($item['item_name']) ?></h3>
                                <p class="desc"><?= htmlspecialchars($item['description'] ?: 'No description.') ?></p>
                                <div class="price"><?= number_format($item['price'],2) ?> BD</div>
                            </div>
                            <div class="card-actions">
                                <form method="POST">
                                    <!-- CSRF token for add-to-cart -->
                                    <input type="hidden" name="csrf_token"
                                           value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                    <input type="hidden" name="menu_id" value="<?= $item['menu_id'] ?>">
                                    <input type="number" name="quantity" value="1" min="1">
                                    <button type="submit" name="add_to_cart">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    </main>
</body>
</html>
