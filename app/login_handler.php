<?php
require_once 'includes/session_config.php';
require_once 'includes/csrf.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'] ?? '';
$name  = $data['user_metadata']['full_name'] ?? 'Google User';

$conn = new mysqli("db", "root", "rootpass", "RestaurantDB");
if ($conn->connect_error) die("Connection failed");


$stmt = $conn->prepare("SELECT * FROM google_login WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    $_SESSION['user_id'] = $user['customer_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['login_type'] = 'google';
} else {
 
    $original_name = $name;
    $check = $conn->prepare("SELECT 1 FROM google_login WHERE username = ?");
    $check->bind_param("s", $name);
    $check->execute();
    $check->store_result();

    $suffix = 1;
    while ($check->num_rows > 0) {
        $name = $original_name . rand(1000, 9999);
        $check->bind_param("s", $name);
        $check->execute();
        $check->store_result();
    }

   
    $stmt = $conn->prepare("INSERT INTO google_login (username, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $email);
    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['username'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['login_type'] = 'google';
    } else {
        http_response_code(500);
        echo "Failed to insert user.";
        exit();
    }
}

http_response_code(200);
