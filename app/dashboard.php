<?php
require __DIR__ . '/includes/session_config.php';
require __DIR__ . '/includes/csrf.php';

if (!isset($_SESSION["employee_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    validate_csrf_token();
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

$role = $_SESSION["role"] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Dashboard - Bella Pizza</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #dc3545;
            --accent: #f8c102;
            --light: #fff;
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
            color: #222;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
        }
        .dashboard {
            max-width: 500px;
            width: 100%;
            background: var(--light);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.08);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header h2 {
            font-size: 1.5rem;
            color: var(--primary);
        }
        .logout-form button {
            background-color: var(--primary);
            color: var(--light);
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .logout-form button:hover {
            background-color: #a71d2a;
        }
        .welcome {
            margin-bottom: 25px;
            text-align: center;
        }
        .welcome p {
            font-size: 15px;
            color: #444;
        }
        .actions {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .action-button {
            display: block;
            width: 100%;
            text-align: center;
            background: var(--primary);
            color: var(--light);
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
            transition: background 0.2s ease;
            font-size: 16px;
        }
        .action-button:hover {
            background: #a71d2a;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 13px;
            color: #666;
        }

        @media (max-width: 600px) {
            .dashboard {
                padding: 20px;
            }
            .action-button {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <div class="header">
            <h2><?= ucfirst($role) ?> Dashboard</h2>
            <form method="POST" class="logout-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <button type="submit" name="logout">Logout</button>
            </form>
        </div>

        <div class="welcome">
            <p>You are logged in as <strong><?= htmlspecialchars($_SESSION["username"]) ?></strong>.</p>
        </div>

        <div class="actions">
            <?php if ($role === 'admin'): ?>
                <a href="edit_menu_items.php" class="action-button">🍕 Manage Menu</a>
                <a href="orders.php" class="action-button">📦 Manage Orders</a>
                <a href="employees.php" class="action-button">👥 Employees</a>
                <a href="analytics.php" class="action-button">📊 Business Analytics</a>
                <a href="logs.php" class="action-button">🧾 View System Logs</a>
            <?php elseif ($role === 'support'): ?>
                <a href="edit_menu_items.php" class="action-button">🍕 Manage Menu</a>
                <a href="orders.php" class="action-button">📦 Manage Orders</a>
            <?php elseif (in_array($role, ['cashier', 'chef', 'delivery'])): ?>
                <a href="orders.php" class="action-button">📦 View Orders</a>
                <?php if ($role === 'delivery'): ?>
                    <a href="driver_share_location.php" class="action-button">📍 Share Location</a>
                <?php endif; ?>
            <?php else: ?>
                <p style="text-align:center;">No actions available for your role.</p>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>&copy; <?= date("Y") ?> Bella Pizza</p>
        </div>
    </div>
</body>
</html>
