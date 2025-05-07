<?php
require_once 'includes/session_config.php';


if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}


$access_code = "IFafg4g3ZkDD5SigYlpl";
$merchant_identifier = "pCNttJRP";
$sha_request_phrase = "12QN0MFnhxftV7Rev1l2nY[!";
$currency = "BHD";
$language = "en";
$return_url = "http://localhost:8080/response.php?session_id=" . session_id(); 


$cart_total_bhd = $_SESSION['cart_total_bhd'];
$amount = $cart_total_bhd * 1000; // Convert to fils
$order_id = uniqid("BPZ_");


$request_params = [
    'access_code'         => $access_code,
    'merchant_identifier' => $merchant_identifier,
    'merchant_reference'  => $order_id,
    'amount'              => $amount,
    'currency'            => $currency,
    'language'            => $language,
    'command'             => 'PURCHASE',
    'return_url'          => $return_url,
    'customer_email'      => $_SESSION['email'] ?? 'guest@bellapizza.com',
];


ksort($request_params);

$signature_string = $sha_request_phrase;
foreach ($request_params as $key => $value) {
    $signature_string .= "$key=$value";
}
$signature_string .= $sha_request_phrase;

$signature = hash("sha256", $signature_string);


?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="refresh" content="0;url=https://sbcheckout.payfort.com/FortAPI/paymentPage" />
</head>
<body>
    <form id="paymentForm" method="POST" action="https://sbcheckout.payfort.com/FortAPI/paymentPage">
        <?php foreach ($request_params as $key => $value): ?>
            <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
        <?php endforeach; ?>
        <input type="hidden" name="signature" value="<?= $signature ?>">
    </form>

    <script>
    
        document.getElementById('paymentForm').submit();
    </script>
</body>
</html>
