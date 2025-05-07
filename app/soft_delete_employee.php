<?php
session_start();
if (!isset($_SESSION["employee_id"]) || !in_array($_SESSION["role"], ['admin', 'support'])) {
    http_response_code(403);
    exit("Unauthorized");
}

$conn = new mysqli("db", "root", "rootpass", "RestaurantDB");
if ($conn->connect_error) {
    http_response_code(500);
    exit("DB error");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("UPDATE employee SET is_deleted = 1 WHERE employee_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "OK";
    exit;
}

http_response_code(400);
echo "Bad Request";
