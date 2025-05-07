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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $pdo->prepare(
            "INSERT INTO customer (username, email, phone, address) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$name, $email, $phone, $address]);
        $success = "Customer added successfully!";
    } else {
        $error = "Please provide a valid name and email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Customer — Bella Pizza</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary: #dc3545;
            --light: #fff;
            --bg: #f4f4f4;
            --text: #333;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: var(--light);
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--primary);
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .success { color: green; }
        .error   { color: red; }
        label {
            display: block;
            margin-bottom: 12px;
            font-weight: 500;
        }
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }
        textarea { resize: vertical; min-height: 80px; }
        button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background-color: var(--primary);
            color: var(--light);
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #a71d2a;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: var(--light);
            background: #28a745;
            padding: 10px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link a:hover {
            background-color: #1f8033;
        }
        @media (max-width: 480px) {
            .container { padding: 20px; }
            button, .back-link a { font-size: 0.9rem; padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Customer</h2>

        <?php if (!empty($success)): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php elseif (!empty($error)): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>
                Name
                <input type="text" name="name" required>
            </label>
            <label>
                Email
                <input type="email" name="email" required>
            </label>
            <label>
                Phone (optional)
                <input type="text" name="phone">
            </label>
            <label>
                Address (optional)
                <textarea name="address"></textarea>
            </label>
            <button type="submit">➕ Add Customer</button>
        </form>

        <div class="back-link">
            <a href="customers.php">← Back to Customer List</a>
        </div>
    </div>
</body>
</html>
