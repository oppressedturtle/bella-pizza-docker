<?php
require_once 'includes/session_config.php';
require_once 'includes/csrf.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

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

$missingProfile = false;
$loginType = $_SESSION['login_type'] ?? 'normal';
$user_id = $_SESSION['user_id'];

if ($loginType === 'google') {
    $stmt = $pdo->prepare("SELECT phone, address FROM google_login WHERE customer_id = ?");
} else {
    $stmt = $pdo->prepare("SELECT phone, address FROM customer WHERE customer_id = ?");
}
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($profile['phone']) || empty($profile['address'])) {
    $missingProfile = true;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    validate_csrf_token();

    if (isset($_POST['update'])) {
        foreach ($_POST['quantities'] as $menu_id => $qty) {
            $qty = max(0, (int)$qty);
            if ($qty === 0) {
                unset($_SESSION['cart'][$menu_id]);
            } else {
                $_SESSION['cart'][$menu_id] = $qty;
            }
        }
    } elseif (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
    }
}

$cart = $_SESSION['cart'];
$menu_items = [];
$total = 0;

if (!empty($cart)) {
    $placeholders = implode(',', array_fill(0, count($cart), '?'));
    $stmt = $pdo->prepare("SELECT * FROM menu WHERE menu_id IN ($placeholders)");
    $stmt->execute(array_keys($cart));
    $menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart - Bella Pizza</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
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
            padding: 0;
        }

        .navbar {
            background: var(--primary);
            color: var(--light);
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 { font-size: 1.5rem; margin: 0; }
        .navbar .actions button {
            background: var(--accent);
            color: black;
            border: none;
            padding: 8px 16px;
            font-weight: bold;
            border-radius: 6px;
            margin-left: 10px;
            cursor: pointer;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            background: var(--light);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            color: #222;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background-color: var(--primary);
            color: var(--light);
        }

        tr:hover {
            background-color: #fdf2f2;
        }

        input[type="number"] {
            width: 60px;
            padding: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
            text-align: center;
        }

        button {
            background-color: var(--primary);
            color: var(--light);
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }

        button:hover {
            background-color: #a71d2a;
        }

        .danger {
            background-color: #dc3545;
        }

        .danger:hover {
            background-color: #a71d2a;
        }

        .actions {
            margin-top: 20px;
            text-align: center;
        }

        .inline-buttons form {
            display: inline-block;
            margin: 0 10px;
        }

        .links {
            text-align: center;
            margin-top: 30px;
        }

        .links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: bold;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .message.warning {
            background: #fff3cd;
            color: #856404;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ffeeba;
            margin-bottom: 15px;
            text-align: center;
        }

        @media (max-width: 700px) {
            table, th, td {
                font-size: 14px;
            }
            input[type="number"] {
                width: 50px;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Bella Pizza 🍕</h1>
        <div class="actions">
            <button onclick="location.href='dashboard_user.php'">Dashboard</button>
            <button onclick="location.href='logout.php'">Logout</button>
        </div>
    </div>

    <div class="container">
        <h2>Your Cart</h2>

        <?php if (empty($cart)): ?>
            <p style="text-align:center;">Your cart is empty.</p>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <table>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                    <?php foreach ($menu_items as $item):
                        $menu_id = $item['menu_id'];
                        $qty = $cart[$menu_id];
                        $subtotal = $qty * $item['price'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                            <td><?= number_format($item['price'], 2) ?> BD</td>
                            <td><input type="number" name="quantities[<?= $menu_id ?>]" value="<?= $qty ?>" min="0"></td>
                            <td><?= number_format($subtotal, 2) ?> BD</td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" style="text-align:right;"><strong>Total:</strong></td>
                        <td><strong><?= number_format($total, 2) ?> BD</strong></td>
                    </tr>
                </table>

                <div class="actions">
                    <button type="submit" name="update">🔄 Update Cart</button>
                    <button type="submit" name="clear_cart" class="danger" onclick="return confirm('Clear your entire cart?');">🗑️ Clear Cart</button>
                </div>
            </form>

            <div class="actions inline-buttons" style="margin-top: 20px;">
                <?php $_SESSION['cart_total_bhd'] = $total; ?>
                <?php if ($missingProfile): ?>
                    <div class="message warning">
                        ⚠️ Please complete your profile (phone number and address) before proceeding to payment.
                        <br><br>
                        <button type="button" onclick="window.location.href='profile.php'">Go to Profile</button>
                    </div>
                <?php else: ?>
                    <form action="Paymentgateway.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <button type="submit">💳 Proceed to Payment</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="links">
            <a href="dashboard_user.php">← Back to Menu</a>
        </div>
    </div>
</body>
</html>
