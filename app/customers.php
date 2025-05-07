<?php
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

// Handle customer deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM customer WHERE customer_id = ?");
    $stmt->execute([$_POST['delete_id']]);
    $message = "Customer deleted successfully.";
}

// Fetch all customers
$customers = $pdo->query("SELECT * FROM customer ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customers</title>
</head>
<body>
    <h2>Customer List</h2>

    <?php if (!empty($message)) echo "<p style='color: green;'>$message</p>"; ?>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Registered</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($customers as $customer): ?>
        <tr>
            <td><?= $customer['customer_id'] ?></td>
            <td><?= htmlspecialchars($customer['username']) ?></td>
            <td><?= htmlspecialchars($customer['email']) ?></td>
            <td><?= htmlspecialchars($customer['phone']) ?></td>
            <td><?= htmlspecialchars($customer['address']) ?></td>
            <td><?= $customer['created_at'] ?></td>
            <td>
                <!-- Edit button -->
                <a href="edit_customer.php?customer_id=<?= $customer['customer_id'] ?>">
                    <button>Edit</button>
                </a>

                <!-- Delete button -->
                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                    <input type="hidden" name="delete_id" value="<?= $customer['customer_id'] ?>">
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <br>
    <form action="add_customer.php" method="get" style="display:inline;">
        <button type="submit">Add customer</button>
    </form>
    <br>
    <a href="dashboard.php">← Back to Dashboard</a>
</body>
</html>
