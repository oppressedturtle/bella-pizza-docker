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
if (!isset($_SESSION["employee_id"]) || !in_array($_SESSION["role"], ['admin', 'support'])) {
    header("Location: login.php");
    exit();
}


if (!isset($_GET['customer_id'])) {
    die("Customer ID not specified.");
}

$customer_id = $_GET['customer_id'];
$success = $error = "";
$stmt = $pdo->prepare("SELECT * FROM customer WHERE customer_id = ?");
$stmt->execute([$customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    die("Customer not found.");
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_customer"])) {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $new_password = $_POST["password"];

    if (!empty($username) && !empty($email)) {
        if (!empty($new_password)) {
          
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE customer SET username = ?, email = ?, phone = ?, address = ?, password_hash = ? WHERE customer_id = ?");
            $stmt->execute([$username, $email, $phone, $address, $password_hash, $customer_id]);
        } else {
          
            $stmt = $pdo->prepare("UPDATE customer SET username = ?, email = ?, phone = ?, address = ? WHERE customer_id = ?");
            $stmt->execute([$username, $email, $phone, $address, $customer_id]);
        }

        $success = "Customer updated successfully.";

      
        $stmt = $pdo->prepare("SELECT * FROM customer WHERE customer_id = ?");
        $stmt->execute([$customer_id]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $error = "Username and email are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Customer</title>
</head>
<body>
    <h2>Edit Customer</h2>

    <?php if ($success): ?>
        <p style="color: green;"><?= $success ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Username: <input type="text" name="username" value="<?= htmlspecialchars($customer['username']) ?>" required></label><br><br>
        <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($customer['email']) ?>" required></label><br><br>
        <label>Phone: <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>"></label><br><br>
        <label>Address:<br>
            <textarea name="address" rows="4" cols="40"><?= htmlspecialchars($customer['address']) ?></textarea>
        </label><br><br>
        <label>New Password (leave blank to keep current): <input type="password" name="password"></label><br><br>
        <button type="submit" name="update_customer">Update Customer</button>
    </form>

    <br>
    <a href="customers.php"> Back to Customer List</a>
</body>
</html>
