<?php
require __DIR__ . '/includes/session_config.php';
require __DIR__ . '/includes/csrf.php';

$host = "db";
$dbname = "RestaurantDB";
$username = "root";
$password = "rootpass";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    validate_csrf_token();
    session_destroy();
    header("Location: login.php");
    exit();
}

$ip = $_SERVER['REMOTE_ADDR'];
$userAgent = $_SERVER['HTTP_USER_AGENT'];

if (!isset($_COOKIE['visitor_logged'])) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO visitors (ip_address, user_agent) VALUES (?, ?)");
    $stmt->execute([$ip, $userAgent]);
    setcookie('visitor_logged', '1', time() + (86400 * 30), "/");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Welcome to Bella Pizza</title>
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
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
      flex-direction: column;
    }

    .logo {
      margin-bottom: 20px;
      max-width: 180px;
    }

    .logo img {
      width: 100%;
      height: auto;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .container {
      background-color: var(--light);
      padding: 40px 30px;
      border-radius: 16px;
      max-width: 700px;
      width: 100%;
      text-align: center;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    h1 {
      font-size: 2.8rem;
      color: var(--primary);
      margin-bottom: 20px;
    }

    p {
      font-size: 1.15rem;
      color: #444;
      margin-bottom: 30px;
      max-width: 550px;
      margin-left: auto;
      margin-right: auto;
    }

    .button-group {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 16px;
      margin-top: 20px;
    }

    .button-group a,
    .button-group form button {
      background-color: var(--primary);
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      color: white;
      font-weight: bold;
      text-decoration: none;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.25s ease;
    }

    .button-group a:hover,
    .button-group form button:hover {
      background-color: #a71d2a;
    }

    .button-group form {
      display: inline;
    }

    @media (max-width: 600px) {
      .container {
        padding: 25px 20px;
      }

      .logo {
        max-width: 140px;
      }

      h1 {
        font-size: 2rem;
      }

      p {
        font-size: 1rem;
      }

      .button-group a,
      .button-group form button {
        font-size: 15px;
        padding: 10px 18px;
      }
    }
  </style>
</head>
<body>

<div class="logo">
  <img src="img/bella pizza logo.png" alt="Bella Pizza Logo">
</div>

<div class="container">
  <h1>Welcome to Bella Pizza</h1>
  <p>Delicious, handcrafted pizzas made with love — explore our menu and treat yourself today.</p>

  <div class="button-group">
    <?php if (!isset($_SESSION["employee_id"]) && !isset($_SESSION["user_id"])): ?>
      <a href="login.php">Login</a>
    <?php else: ?>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <button type="submit" name="logout">Logout</button>
      </form>
    <?php endif; ?>
    <a href="menu.php">View Menu</a>
  </div>
</div>

</body>
</html>
