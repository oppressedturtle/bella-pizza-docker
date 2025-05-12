<?php
require_once 'includes/session_config.php';
require_once 'includes/csrf.php';

if (!isset($_SESSION['employee_id']) || $_SESSION['role'] !== 'delivery') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Share Location - Bella Pizza</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: sans-serif;
      text-align: center;
      padding: 40px 20px;
      background: #fff8f3;
    }
    h2 {
      color: #dc3545;
    }
    .status {
      margin-top: 20px;
      font-weight: bold;
    }
    .ok {
      color: green;
    }
    .error {
      color: red;
    }
    .info {
      margin-top: 10px;
      font-size: 14px;
      color: #666;
    }
  </style>
</head>
<body>
  <h2>Live Location Sharing</h2>
  <p>This page will continuously send your real-time GPS position.</p>
  <div id="status" class="status">⏳ Waiting for GPS fix...</div>
  <div class="info">Please keep this page open during delivery.</div>

  <script>
    const status = document.getElementById("status");

    function sendLocation(lat, lng) {
      fetch("driver_update_location.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ latitude: lat, longitude: lng })
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === "ok") {
          status.textContent = `📍 Sent: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
          status.className = "status ok";
        } else {
          status.textContent = "❌ Failed to send location";
          status.className = "status error";
        }
      })
      .catch(() => {
        status.textContent = "❌ Network error";
        status.className = "status error";
      });
    }

    function startTracking() {
      if (!navigator.geolocation) {
        status.textContent = "❌ Geolocation not supported";
        status.className = "status error";
        return;
      }

      
      navigator.geolocation.getCurrentPosition(
        pos => {
          sendLocation(pos.coords.latitude, pos.coords.longitude);
        },
        err => {
          status.textContent = "❌ Initial fix error: " + err.message;
          status.className = "status error";
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
      );

      // Continuous tracking
      navigator.geolocation.watchPosition(
        pos => {
          sendLocation(pos.coords.latitude, pos.coords.longitude);
        },
        err => {
          status.textContent = "❌ GPS error: " + err.message;
          status.className = "status error";
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
      );
    }

    startTracking();
  </script>
</body>
</html>