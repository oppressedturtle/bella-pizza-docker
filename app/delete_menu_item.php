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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["menu_id"])) {
    $menu_id = $_POST["menu_id"];

    try {
        $stmt = $pdo->prepare("DELETE FROM menu WHERE menu_id = ?");
        $stmt->execute([$menu_id]);
        header("Location: edit_menu_items.php?success=Item deleted successfully");
    } catch (PDOException $e) {
        header("Location: edit_menu_items.php?error=Cannot delete this item. It has existing orders.");
    }
    exit();
}

header("Location: edit_menu_items.php");
exit();
