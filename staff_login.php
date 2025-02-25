<?php
session_start();
require 'db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM staff WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $rowPassword = $row['password'];
        $_SESSION['staffData'] = $row;

        if (password_verify($password, $rowPassword)) {
            echo "<script>alert('Login successful!');</script>";
            header("Location: staff_home.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password.');</script>";
        }
    } else {
        echo "<script>alert('No account found with this email. Please register.');</script>";
        echo "<script>window.location.href='staff_register.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('bg2.png') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgb(236, 242, 243);
            backdrop-filter: blur(0.2px);
        }
        .login-container {
            z-index: 1; /* Position it above the blurred background */
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 90%;
            max-width: 400px;
            animation: fadeIn 1s ease-in-out;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        .login-container h2 {
            font-size: 24px;
            margin: 10px 0;
            color: #4a90e2;
        }
        .login-container .input-container {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
            margin: 5px 0;
        }
        .login-container .input-container .icon {
            padding: 10px;
            background: #4a90e2;
            color: white;
            min-width: 50px;
            text-align: center;
            border-radius: 10px 0 0 10px;
        }
        .login-container .input-container input[type="email"],
        .login-container .input-container input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 0 10px 10px 0;
            box-sizing: border-box;
        }
        .login-container button {
            width: 100%;
            padding: 12px;
            background: #4a90e2;
            border: none;
            color: white;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        .login-container button:hover {
            background: #357ab7;
        }
        .login-container p {
            margin-top: 10px;
            color: #4a90e2;
        }
        .login-container p a {
            color: #357ab7;
            text-decoration: none;
        }
        .login-container p a:hover {
            text-decoration: underline;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #4a90e2;
        }
        /* Responsive design */
        @media (max-width: 768px) {
            .login-container {
                padding: 15px;
                width: 90%;
                max-width: 300px;
            }
            .login-container h2 {
                font-size: 20px;
            }
            .login-container button {
                padding: 10px;
                font-size: 14px;
            }
        }
        @media (max-width: 480px) {
            .login-container {
                padding: 10px;
                width: 90%;
                max-width: 280px;
            }
            .login-container h2 {
                font-size: 18px;
            }
            .login-container button {
                padding: 8px;
                font-size: 12px;
            }
        }
    </style>
    <script>
        function togglePasswordVisibility(id) {
            const passwordField = document.getElementById(id);
            passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
        }
    </script>
</head>
<body>
    <div class="overlay"></div>
    <div class="login-container">
        <h2>Staff Login</h2>
        <form method="POST" action="staff_login.php">
            <div class="input-container">
                <span class="icon">📧</span>
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            <div class="input-container">
                <span class="icon">🔒</span>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePasswordVisibility('password')">👁️</span>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
        <p>Don't have an Account? <a href="staff_register.php">Register here</a></p>
    </div>
</body>
</html>
