<?php
$conn = new mysqli("localhost", "root", "", "clinicdatabse");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $mobile = $_POST['mobile'];

    if ($password != $confirmPassword) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO staff (fullName, email, password, mobile) VALUES ('$fullName', '$email', '$hashedPassword', '$mobile')";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Registration successful!');</script>";
            echo "<script>window.location.href='staff_login.php';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Registration</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('background_image.jpg') no-repeat center center fixed;
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
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
        }
        .registration-container {
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
        .registration-container h2 {
            font-size: 24px;
            margin: 10px 0;
            color: #4a90e2;
        }
        .registration-container .input-container {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
            margin: 5px 0;
        }
        .registration-container .input-container .icon {
            padding: 10px;
            background: #4a90e2;
            color: white;
            min-width: 50px;
            text-align: center;
            border-radius: 10px 0 0 10px;
        }
        .registration-container .input-container input[type="text"],
        .registration-container .input-container input[type="email"],
        .registration-container .input-container input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 0 10px 10px 0;
            box-sizing: border-box;
        }
        .registration-container button {
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
        .registration-container button:hover {
            background: #357ab7;
        }
        .registration-container p {
            margin-top: 10px;
            color: #4a90e2;
        }
        .registration-container p a {
            color: #357ab7;
            text-decoration: none;
        }
        .registration-container p a:hover {
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
            .registration-container {
                padding: 15px;
                width: 90%;
                max-width: 300px;
            }
            .registration-container h2 {
                font-size: 20px;
            }
            .registration-container button {
                padding: 10px;
                font-size: 14px;
            }
        }
        @media (max-width: 480px) {
            .registration-container {
                padding: 10px;
                width: 90%;
                max-width: 280px;
            }
            .registration-container h2 {
                font-size: 18px;
            }
            .registration-container button {
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
    <div class="registration-container">
        <h2>Staff Registration</h2>
        <form method="POST" action="staff_register.php">
            <div class="input-container">
                <span class="icon">👤</span>
                <input type="text" name="fullname" placeholder="Full Name" required>
            </div>
            <div class="input-container">
                <span class="icon">📧</span>
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            <div class="input-container">
                <span class="icon">🔒</span>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePasswordVisibility('password')">👁️</span>
            </div>
            <div class="input-container">
                <span class="icon">🔒</span>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                <span class="toggle-password" onclick="togglePasswordVisibility('confirm_password')">👁️</span>
            </div>
            <button type="submit" name="register">Register</button>
        </form>
        <p>Already have an account? <a href="staff_login.php">Login here</a></p>
    </div>
</body>
</html>
