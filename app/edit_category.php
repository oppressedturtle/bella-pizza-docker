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

require_once "log_helper.php";

if (!isset($_SESSION["employee_id"]) || !in_array($_SESSION["role"], ['admin', 'support'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("No category ID provided.");
}

$category_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM category WHERE category_id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    die("Category not found.");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $update = $pdo->prepare("UPDATE category SET name = ? WHERE category_id = ?");
        $update->execute([$name, $category_id]);

        // Log the update
        log_action("Update Category", "Category ID $category_id renamed to '$name'", "INFO", $_SESSION["employee_id"], null, $_SESSION["username"]);

        $message = "Category updated!";
        header("Location: categories.php");
        exit;
    } else {
        $message = "Please enter a category name.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Category | Bella Pizza</title>
    <style>
        :root {
            --primary: #dc3545;
            --light: #fff;
            --accent: #28a745;
            --bg: #f4f4f4;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            width: 90%;
            max-width: 500px;
            background: var(--light);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: var(--primary);
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button {
            width: 100%;
            background-color: var(--primary);
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #a71d2a;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            display: inline-block;
            text-decoration: none;
            background: var(--accent);
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            font-weight: bold;
        }
        .back-link a:hover {
            background-color: #1f8033;
        }
        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 15px;
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Category</h2>

        <?php if (!empty($message)): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="name">Category Name:</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($category['name']) ?>" required>
            <button type="submit">Update Category</button>
        </form>

        <div class="back-link">
            <a href="categories.php">← Back to Categories</a>
        </div>
    </div>
</body>
</html>
