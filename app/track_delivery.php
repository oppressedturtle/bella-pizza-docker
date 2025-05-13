<?php
require_once 'includes/session_config.php';
require_once 'includes/csrf.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$order_id  = isset($_GET['order_id'])  ? (int)$_GET['order_id']  : 0;
$driver_id = isset($_GET['driver_id']) ? (int)$_GET['driver_id'] : 0;

$pdo = new PDO("mysql:host=db;dbname=RestaurantDB", "root", "rootpass");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$stmt = $pdo->prepare("SELECT delivery_driver_id FROM `order` WHERE order_id = ? AND customer_id = ?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order || (int)$order['delivery_driver_id'] !== $driver_id) {
    die("Unauthorized access.");
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Track Delivery</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      font-family: sans-serif;
    }

    #map {
      height: 100%;
      width: 100%;
    }

    .top-bar {
      position: absolute;
      top: 15px;
      left: 100px;
      z-index: 999;
    }

    .back-button {
      background-color: #dc3545;
      color: white;
      padding: 10px 16px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }

    .back-button:hover {
      background-color: #a71d2a;
    }

    .title-banner {
      position: absolute;
      top: 15px;
      right: 15px;
      background: rgba(255, 255, 255, 0.9);
      padding: 10px 16px;
      border-radius: 8px;
      font-weight: bold;
      color: #dc3545;
      z-index: 999;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>

<div class="top-bar">
  <a href="my_orders.php" class="back-button">⬅ Back to My Orders</a>
</div>

<div class="title-banner">
  Tracking Order #<?= htmlspecialchars($order_id) ?>
</div>

<div id="map"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
  const map = L.map('map').setView([26.2285, 50.5860], 12);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
  let marker;

  async function fetchLocation() {
    const res = await fetch('get_driver_location.php?driver_id=<?= $driver_id ?>');
    const data = await res.json();
    if (data.latitude && data.longitude) {
      const pos = [data.latitude, data.longitude];
      if (!marker) {
        marker = L.marker(pos).addTo(map).bindPopup("Driver Location").openPopup();
      } else {
        marker.setLatLng(pos);
      }
      map.setView(pos, 14);
    }
  }

  fetchLocation();
  setInterval(fetchLocation, 10000);
</script>
</body>
</html>
