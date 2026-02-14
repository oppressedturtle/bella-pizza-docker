<?php
session_start();
header("Content-Type: application/json");


if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['message'])) {
    echo json_encode(['reply' => 'Invalid request.']);
    exit();
}


$message = trim($_POST['message']);
$role = $_SESSION['role'] ?? 'customer';


$systemPrompt = ($role === 'admin')
    ? "You are a helpful assistant for Bella Pizza's admin panel. Assist with orders, employee management, and revenue insights."
    : "You are Bella Pizza's chatbot. greet users and give them a single recommendation based on what they menu item they tell you, and asnwer them if they inquire about the price, your answers should be very short.";


$apiKey = 'FAKE';


$ch = curl_init("https://openrouter.ai/api/v1/chat/completions");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey",
        "HTTP-Referer: https://yourdomain.com", 
        "X-Title: BellaPizzaChatbot"
    ],
    CURLOPT_POSTFIELDS => json_encode([
        "model" => "mistralai/mistral-7b-instruct",
        "max_tokens" => 250, 
        "messages" => [
            ["role" => "system", "content" => $systemPrompt],
            ["role" => "user", "content" => $message]
        ]
    ])
]);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if ($error || !$response) {
    echo json_encode(['reply' => 'Error contacting AI server.']);
    exit();
}

$data = json_decode($response, true);
$reply = $data['choices'][0]['message']['content'] ?? "Sorry, I couldn't respond right now.";

echo json_encode(['reply' => $reply]);
