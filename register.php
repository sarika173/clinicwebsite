<?php
// Database connection
session_start();
require 'db.php';
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $fullName = $_POST['fullname'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $age = $_POST['age'];
    $mobile = trim($_POST['mobile_number']); // Trim spaces
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];

    // Validate if password and confirm password match
    if ($password != $confirmPassword) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        // Validate mobile number to be exactly 10 digits and numeric
        if (!preg_match("/^\d{10}$/", $mobile)) {
            echo "<script>alert('Please enter a valid 10-digit mobile number!');</script>";
        } else {
            // Check if the email already exists
            $checkEmailQuery = "SELECT * FROM registration WHERE email = '$email'";
            $result = $conn->query($checkEmailQuery);
            
            if ($result->num_rows > 0) {
                echo "<script>alert('An account with this email already exists!');</script>";
            } else {
                // Hash the password before inserting into the database
                $hashedPassword = $password;

                // Insert data into the database
                $sql = "INSERT INTO registration (`fullName`, `password`, `age`, `mobile`, `email`, `gender`, `address`) 
                        VALUES ('$fullName', '$hashedPassword', '$age', '$mobile', '$email', '$gender', '$address')";
                
                if ($conn->query($sql) === TRUE) {
                    echo "<script>alert('Registration successful!');</script>";
                    echo "<script>window.location.href='login.php';</script>";  // Redirect to login page after successful registration
                } else {
                    echo "<script>alert('Error: " . $conn->error . "');</script>";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('bg2.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
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
        .background-blur {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.2);
        }
        .form-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 10px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 80%;
            max-width: 450px;
            animation: fadeIn 1s ease-in-out;
            z-index: 1;
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
        .form-container h2 {
            font-size: 24px;
            margin: 10px 0;
            color: #4a90e2;
        }
        .form-container .input-container {
            position: relative;
            width: 100%;
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        .form-container .input-container .icon {
            padding: 10px;
            background: #4a90e2;
            color: white;
            min-width: 50px;
            text-align: center;
            border-radius: 10px 0 0 10px;
        }
        .form-container .input-container input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 0 10px 10px 0;
            box-sizing: border-box;
        }
        .form-container input, .form-container select, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-sizing: border-box;
        }
        .form-container button {
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
        .form-container button:hover {
            background: #357ab7;
        }
        .form-container p {
            margin-top: 10px;
            color: #4a90e2;
        }
        .form-container p a {
            color: #357ab7;
            text-decoration: none;
        }
        .form-container p a:hover {
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
    <script>
        function togglePasswordVisibility(id) {
            const passwordField = document.getElementById(id);
            passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Register</h2>
        <form method="POST" action="">
            <input type="text" name="fullname" placeholder="Full Name" required>
            <div class="password-container input-container">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <span class="toggle-password" onclick="togglePasswordVisibility('password')">👁️</span>
            </div>
            <div class="password-container input-container">
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                <span class="toggle-password" onclick="togglePasswordVisibility('confirm_password')">👁️</span>
            </div>
            <input type="number" name="age" placeholder="Age" required>
            <input type="text" name="mobile_number" placeholder="Mobile Number" pattern="[0-9]{10}" required>
            <input type="email" name="email" placeholder="Email ID" required>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <textarea name="address" placeholder="Address" required></textarea>
            <button type="submit" name="register">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
