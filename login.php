<?php
session_start();
require 'db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sendOTPEmail($email, $otp) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.example.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your_email@example.com';
        $mail->Password   = 'your_password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('your_email@example.com', 'Your Name');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Password Reset';
        $mail->Body    = 'Your OTP is ' . $otp;

        $mail->send();
        echo 'OTP has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function generateOTP() {
    return rand(100000, 999999);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM registration WHERE `email` = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $rowPassword = $row['password'];
        $_SESSION['userData'] = $row;

        if ($rowPassword == $password) {
            echo "<script>alert('Login successful!');</script>";
            header("Location: patient_home.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password.');</script>";
        }
    } else {
        echo "<script>alert('No account found with this email. Please register.');</script>";
        echo "<script>window.location.href='register.php';</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_otp'])) {
    $email = $_POST['email'];
    $otp = generateOTP();
    $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    $sql = "UPDATE registration SET otp = '$otp', otp_expiry = '$expiry' WHERE email = '$email'";
    if ($conn->query($sql) === TRUE) {
        sendOTPEmail($email, $otp);
        $_SESSION['email'] = $email;
        echo "<script>alert('OTP sent to your email!');</script>";
        echo "<script>window.location.href='verify_otp.php';</script>";
    } else {
        echo "<script>alert('Error updating OTP.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Skin Clinic - Patient Portal</title>
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
            position: relative;
        }
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(236, 242, 243, 0.5); /* Adjust opacity for better visibility */
            backdrop-filter: blur(5px); /* Increase blur effect */
            z-index: 0; /* Ensure it is behind the other content */
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
        .logo-container {
            margin-bottom: 20px;
            animation: fadeIn 1s ease-in-out;
            z-index: 1; /* Ensure the logo is above the overlay */
        }
        .logo-container img {
            width: 100px;
            height: 100px;
            border-radius: 50%; /* Round the image */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Add shadow */
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 400px;
            animation: fadeIn 1s ease-in-out;
        }
        .login-container h1 {
            font-size: 28px;
            margin: 15px 0;
            color: #4a90e2;
        }
        .login-container .input-container {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
            margin: 15px 0;
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
        .login-container a {
            display: block;
            margin: 10px 0;
            color: #4a90e2;
            text-decoration: none;
            font-size: 14px;
        }
        .login-container a:hover {
            text-decoration: underline;
        }
        /* Responsive design */
        @media (max-width: 768px) {
            .form-container {
                padding: 15px;
                width: 90%;
                max-width: 300px;
            }
            .form-container h2 {
                font-size: 20px;
            }
            .form-container button {
                padding: 10px;
                font-size: 14px;
            }
        }
        @media (max-width: 480px) {
            .form-container {
                padding: 10px;
                width: 90%;
                max-width: 280px;
            }
            .form-container h2 {
                font-size: 18px;
            }
            .form-container button {
                padding: 8px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="overlay"></div> <!-- Add this line to include the overlay -->
    <div class="logo-container">
        <img src="logo.jpg" alt="The Skin Clinic Logo">
    </div>
    <div class="login-container">
        <h1>Login</h1>
        <p>Patient Portal</p>
        <form method="POST" action="login.php">
            <div class="input-container">
                <span class="icon">📧</span>
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            <div class="input-container">
                <span class="icon">🔒</span>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
        <a href="forgot_password.php">Forgot Password?</a>
        Don't have an Account? <a href="register.php">Register</a>
    </div>
    <script>
        document.querySelector('form').addEventListener('submit', function(event) {
            var email = document.querySelector('input[type="email"]').value;
            var password = document.querySelector('input[type="password"]').value;

            if (!email || !password) {
                event.preventDefault();
                alert('Please fill in both fields.');
            }
        });
    </script>
</body>
</html>
