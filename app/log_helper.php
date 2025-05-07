<?php
function log_action($action, $description, $level = 'INFO', $employee_id = null, $customer_id = null, $username = null) {
    static $pdo = null;

    if (!$pdo) {
        try {
            $pdo = new PDO("mysql:host=db;dbname=RestaurantDB", "root", "rootpass");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Logging DB connection failed: " . $e->getMessage());
            return;
        }
    }

    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $session_id = session_id() ?: 'no-session';
    $location   = $_SERVER['REQUEST_URI'] ?? 'unknown';
    $log_time   = date('Y-m-d H:i:s');

    try {
        $stmt = $pdo->prepare("
            INSERT INTO logs 
            (action, description, log_level, employee_id, customer_id, username, ip_address, user_agent, session_id, location, log_time)
            VALUES 
            (:action, :description, :log_level, :employee_id, :customer_id, :username, :ip_address, :user_agent, :session_id, :location, :log_time)
        ");

        $stmt->execute([
            ':action'      => $action,
            ':description' => $description,
            ':log_level'   => strtoupper($level),
            ':employee_id' => $employee_id,
            ':customer_id' => $customer_id,
            ':username'    => $username,
            ':ip_address'  => $ip_address,
            ':user_agent'  => $user_agent,
            ':session_id'  => $session_id,
            ':location'    => $location,
            ':log_time'    => $log_time,
        ]);
    } catch (PDOException $e) {
        error_log("Log insertion failed: " . $e->getMessage());
    }
}
