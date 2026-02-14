<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'noreplybellapizza@gmail.com';
    $mail->Password   = 'FAKE'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('noreplybellapizza@gmail.com', 'Bella Pizza');
    $mail->addAddress($_SESSION['email']);

    $mail->isHTML(true);
    $mail->Subject = 'Your Bella Pizza Order Confirmation 🍕';

    
    $rawAmount = $_POST['amount'] ?? 0;
    $actualAmount = number_format($rawAmount / 1000, 3); 

    $mail->Body = "
        <h2>Thank you for your order!</h2>
        <p>Your payment was successful. We're preparing your pizza now!</p>
        <p><strong>Order ID:</strong> " . htmlspecialchars($_POST['fort_id'] ?? 'N/A') . "<br>
           <strong>Amount:</strong> {$actualAmount} " . htmlspecialchars($_POST['currency'] ?? '') . "</p>
    ";

    $mail->AltBody = "Thank you for your order!\nOrder ID: " . ($_POST['fort_id'] ?? 'N/A') .
                     "\nAmount: {$actualAmount} " . ($_POST['currency'] ?? '');

    $mail->send();
    echo 'Email has been sent ✅';
} catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

