<?php
require __DIR__ . '/includes/session_config.php';
require __DIR__ . '/includes/csrf.php';

$conn = new mysqli("db", "root", "rootpass", "RestaurantDB");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
require_once "log_helper.php";

if (isset($_SESSION["employee_id"])) {
    header("Location: dashboard.php");
    exit();
}
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard_user.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    validate_csrf_token();

    if (isset($_POST["login"])) {
        $username = $conn->real_escape_string($_POST["username"]);
        $password = $_POST["password"];
      
        $query = "SELECT * FROM employee WHERE username='$username'";
        $result = $conn->query($query);
        if ($result->num_rows === 1) {
            $employee = $result->fetch_assoc();
            if (password_verify($password, $employee["password_hash"])) {
                $_SESSION["employee_id"] = $employee["employee_id"];
                $_SESSION["username"]    = $employee["username"];
                $_SESSION["email"]       = $employee["email"];
                $_SESSION["phone"]       = $employee["phone_number"];
                $_SESSION["role"]        = $employee["role"];
                log_action("Login Success", "Employee logged in successfully", "INFO", $employee["employee_id"], null, $employee["username"]);
                header("Location: dashboard.php");
                exit();
            } else {
                log_action("Login Failed", "Invalid password for employee: $username", "WARNING", null, null, $username);
                $error = "Invalid password.";
            }
        } else {
            
            $query  = "SELECT * FROM customer WHERE username='$username'";
            $result = $conn->query($query);
            if ($result->num_rows === 1) {
                $customer = $result->fetch_assoc();
                if (password_verify($password, $customer["password_hash"])) {
                    $_SESSION["user_id"]  = $customer["customer_id"];
                    $_SESSION["username"] = $customer["username"];
                    $_SESSION["email"]    = $customer["email"];
                    $_SESSION["phone"]    = $customer["phone"];
                    log_action("Login Success", "Customer logged in successfully", "INFO", null, $customer["customer_id"], $customer["username"]);
                    header("Location: dashboard_user.php");
                    exit();
                } else {
                    log_action("Login Failed", "Invalid password for customer: $username", "WARNING", null, null, $username);
                    $error = "Invalid password.";
                }
            } else {
                log_action("Login Failed", "Unknown username: $username", "WARNING", null, null, $username);
                $error = "User not found.";
            }
        }
    }

    if (isset($_POST["register"])) {
        $username     = trim($conn->real_escape_string($_POST["reg_username"]));
        $password_raw = trim($_POST["reg_password"]);
        $email        = trim($conn->real_escape_string($_POST["reg_email"]));
        $number       = trim($conn->real_escape_string($_POST["reg_number"]));
        $address      = trim($conn->real_escape_string($_POST["reg_address"]));

      
        if (empty($username) || strlen($username) < 3) {
            $error = "Username must be at least 3 characters long.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email address.";
        } elseif (empty($password_raw) || strlen($password_raw) < 6) {
            $error = "Password must be at least 6 characters long.";
        } elseif (!preg_match('/^[0-9]{8,15}$/', $number)) {
            $error = "Phone number must be between 8 to 15 digits.";
        } elseif (empty($address) || strlen($address) < 5) {
            $error = "Address must be at least 5 characters long.";
        } else {
            $password   = password_hash($password_raw, PASSWORD_BCRYPT);
            $checkUser  = "SELECT * FROM customer WHERE username='$username' OR email='$email'";
            $result     = $conn->query($checkUser);
            if ($result->num_rows == 0) {
                $query = "INSERT INTO customer
                    (username, email, password_hash, phone, address)
                    VALUES ('$username', '$email', '$password', '$number', '$address')";
                if ($conn->query($query)) {
                    log_action("Registration Success", "New customer registered", "INFO", null, $conn->insert_id, $username);
                    $success = "Registration successful. You can now log in.";
                } else {
                    log_action("Registration Error", "Failed to register: $username", "ERROR", null, null, $username);
                    $error = "Registration failed. Please try again.";
                }
            } else {
                log_action("Registration Failed", "Duplicate username/email: $username", "WARNING", null, null, $username);
                $error = "Username or email already exists.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login & Registration - Bella Pizza</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #dc3545;
            --accent: #f8c102;
            --light: #fff;
            --bg: #fff8f3;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Fredoka', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 60px;
        }
        .container {
            background: var(--light);
            border-radius: 12px;
            padding: 30px 40px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #222;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }
        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus {
            border-color: var(--primary);
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background-color: var(--primary);
            color: var(--light);
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        button:hover {
            background-color: #a71d2a;
        }
        .nav {
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .nav button {
            background: #222;
            color: var(--light);
            padding: 8px 14px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .form-section {
            margin-bottom: 40px;
        }
        .message {
            text-align: center;
            margin-bottom: 16px;
            padding: 10px 14px;
            border-radius: 6px;
            font-weight: bold;
        }
        .message.error {
            background: #ffe5e5;
            color: #dc3545;
            border: 1px solid #dc3545;
        }
        .message.success {
            background: #e6f9e6;
            color: #28a745;
            border: 1px solid #28a745;
        }
        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
<div class="nav">
<a href="index.php"><button>← Home</button></a>
</div>

<div class="container">
<div class="form-section">
    <h2>Login</h2>
    <?php if (isset($error))   echo "<div class='message error'>$error</div>"; ?>
    <?php if (isset($success)) echo "<div class='message success'>$success</div>"; ?>
    <form method="POST">
        
        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <label>Username</label>
        <input type="text" name="username" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>

<div class="form-section">
    <h2>Register</h2>
    <form method="POST">
        
        <input type="hidden" name="csrf_token"
               value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <label>Username</label>
        <input type="text" name="reg_username" required>
        <label>Email</label>
        <input type="email" name="reg_email" required>
        <label>Password</label>
        <input type="password" name="reg_password" required>
        <label>Phone Number</label>
        <input type="text" name="reg_number" required>
        <label>Address</label>
        <input type="text" name="reg_address" required>
        <button type="submit" name="register">Register</button>
    </form>
</div>
</div>
</body>
</html>
