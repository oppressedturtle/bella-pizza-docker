<?php
require_once 'includes/session_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("db", "root", "rootpass", "RestaurantDB");

$user_id = $_SESSION['user_id'];
$login_type = $_SESSION['login_type'] ?? 'normal';
$table = ($login_type === 'google') ? 'google_login' : 'customer';

$stmt = $conn->prepare("SELECT * FROM $table WHERE customer_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $username = trim($_POST["username"]);

    if (preg_match('/^[0-9]{8,15}$/', $phone) && strlen($address) >= 5 && strlen($username) >= 3) {
        if ($username !== $user['username']) {
            $check = $conn->prepare("
                SELECT 1 FROM customer WHERE username = ? AND customer_id != ?
                UNION
                SELECT 1 FROM google_login WHERE username = ? AND customer_id != ?
            ");
            $check->bind_param("sisi", $username, $user_id, $username, $user_id);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $error = "That username is already taken.";
            }
        }

        if (!isset($error)) {
            $stmt = $conn->prepare("UPDATE $table SET username = ?, phone = ?, address = ? WHERE customer_id = ?");
            $stmt->bind_param("sssi", $username, $phone, $address, $user_id);
            $stmt->execute();

            $success = "Profile updated successfully.";
            $user["username"] = $username;
            $user["phone"] = $phone;
            $user["address"] = $address;
        }
    } else {
        $error = "Please provide valid information.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Bella Pizza</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #dc3545;
            --accent: #f8c102;
            --light: #fff;
            --bg: #fff8f3;
            --dark: #222;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Fredoka', sans-serif;
            background: var(--bg);
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .header {
            background: var(--primary);
            color: var(--light);
            height: 56px;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .logo span {
            color: var(--accent);
        }

        nav button {
            background: var(--accent);
            color: var(--dark);
            border: none;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.95rem;
            transition: background 0.2s ease;
        }

        nav button:hover {
            background: #ffd43b;
        }

        .container {
            background: var(--light);
            max-width: 500px;
            width: 100%;
            margin: 40px 20px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #222;
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            margin-top: 14px;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        button[type="submit"] {
            width: 100%;
            margin-top: 24px;
            background-color: var(--primary);
            color: var(--light);
            font-weight: bold;
            border: none;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #a71d2a;
        }

        .message {
            margin-top: 10px;
            padding: 12px;
            border-radius: 6px;
            text-align: center;
            font-weight: bold;
        }

        .success {
            background-color: #e6f9e6;
            color: #28a745;
            border: 1px solid #28a745;
        }

        .error {
            background-color: #ffe5e5;
            color: #dc3545;
            border: 1px solid #dc3545;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">Bella <span>Pizza</span> 🍕</div>
        <nav>
            <button onclick="window.location.href='dashboard_user.php'">Dashboard</button>
        </nav>
    </header>

    <div class="container">
        <h2>My Profile</h2>
        <?php if (isset($success)) echo "<div class='message success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='message error'>$error</div>"; ?>
        <form method="POST">
            <label>Name</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

            <label>Email</label>
            <input type="text" value="<?= htmlspecialchars($user['email']) ?>" disabled>

            <label>Phone Number</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>

            <label>Address</label>
            <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>" required>

            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>
