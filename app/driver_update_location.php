<?php
require_once 'includes/session_config.php';
require_once 'includes/csrf.php';

if (!isset($_SESSION['employee_id']) || $_SESSION['role'] !== 'delivery') {
    exit('Unauthorized');
}

$input = json_decode(file_get_contents('php://input'), true);
$lat = $input['latitude'] ?? null;
$lng = $input['longitude'] ?? null;

if (!$lat || !$lng) {
    echo json_encode(['status' => 'error']);
    exit();
}

$pdo = new PDO("mysql:host=db;dbname=RestaurantDB", "root", "rootpass");
$stmt = $pdo->prepare("REPLACE INTO driver_location (driver_id, latitude, longitude, updated_at) VALUES (?, ?, ?, NOW())");
$stmt->execute([$_SESSION['employee_id'], $lat, $lng]);

echo json_encode(['status' => 'ok']);
