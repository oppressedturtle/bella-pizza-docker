<?php
if (isset($_GET['session_id'])) {
    session_id($_GET['session_id']);
}

require_once 'includes/session_config.php';
require_once 'includes/csrf.php';

$sha_response_phrase = '36zZogKxtTYFpxc2lqblun#?';
$response_params = $_POST;

if (empty($response_params)) {
    echo "<h3>No response received.</h3>";
    exit;
}

$received_signature = $response_params['signature'] ?? '';
unset($response_params['signature']);

ksort($response_params);

$signature_string = $sha_response_phrase;
foreach ($response_params as $key => $value) {
    $signature_string .= "$key=$value";
}
$signature_string .= $sha_response_phrase;

$calculated_signature = hash("sha256", $signature_string);
$payment_success = ($received_signature === $calculated_signature && $response_params['status'] === '14');


if ($payment_success && isset($_SESSION['email'])) {
    require_once 'send_email.php';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Response</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding-top: 50px;
            background-color: #1e1e1e;
            color: #f0f0f0;
        }
        .success { color: #4caf50; }
        .fail { color: #f44336; }
        .button {
            margin-top: 30px;
            padding: 10px 20px;
            background-color: #ff6347;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #e5533d;
        }
    </style>
    <?php if ($payment_success): ?>
    <script>
      setTimeout(() => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'place_order.php';

        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = 'from_payment';
        token.value = '1';
        form.appendChild(token);

        document.body.appendChild(form);
        form.submit();
      }, 5000); // delay 5 seconds
    </script>
    <noscript>
      <form method="POST" action="place_order.php">
        <input type="hidden" name="from_payment" value="1">
        <button type="submit">Click here if not redirected</button>
      </form>
    </noscript>
<?php endif; ?>

</head>
<body>

<?php if ($received_signature === $calculated_signature): ?>
    <?php if ($response_params['status'] === '14'): ?>
        <h2 class="success">✅ Payment Successful</h2>
        <p>Thank you for ordering with Bella Pizza!</p>
        <p>A confirmation email has been sent to <strong><?= htmlspecialchars($_SESSION['email']) ?></strong>.</p>
        <p>You will be redirected shortly...</p>
    <?php else: ?>
        <h2 class="fail">❌ Payment Failed</h2>
        <p>Status Code: <?= htmlspecialchars($response_params['status']) ?></p>
        <p>Message: <?= htmlspecialchars($response_params['response_message']) ?></p>
        <a class="button" href="dashboard_user.php">🍕 Return to Home Page</a>
    <?php endif; ?>
<?php else: ?>
    <h2 class="fail">⚠️ Invalid Signature</h2>
    <p>Possible tampering detected.</p>
    <a class="button" href="dashboard_user.php">🍕 Return to Home Page</a>
<?php endif; ?>

</body>
</html>
