<?php

require __DIR__ . '/includes/session_config.php';
require __DIR__ . '/includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    validate_csrf_token();
    echo "✅ CSRF token valid — POST accepted!";
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CSRF Test</title>
</head>
<body>
  <h1>CSRF Helper Test</h1>
  <form method="POST">
    <input type="hidden" name="csrf_token"
           value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <button type="submit">Submit with Token</button>
  </form>
  <p>Try removing or tampering with the hidden <code>csrf_token</code> input and submitting again—you should get an “Invalid CSRF token” error.</p>
</body>
</html>
