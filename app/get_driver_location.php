<?php
$pdo = new PDO("mysql:host=db;dbname=RestaurantDB", "root", "rootpass");
$driver_id = (int) ($_GET['driver_id'] ?? 0);

$stmt = $pdo->prepare("SELECT latitude, longitude FROM driver_location WHERE driver_id = ?");
$stmt->execute([$driver_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($data ?: []);
