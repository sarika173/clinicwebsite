<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "clinicdatabse");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $fullName = $_POST['full_name'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $age = $_POST['age'];
    $mobile = trim($_POST['mobile']); // Trim spaces
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
            // Hash the password before inserting into the database
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert data into the database
            $sql = "INSERT INTO registration (`Full Name`, `Password`, `Age`, `Mobile No`, `E-mail Id`, `Gender`, `Address`) 
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
?>


<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('backgroundimage.jpg');
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
        }
        .form-container {
            width: 400px;
            margin: 100px auto;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input, select, button {
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Register</h2>
        <form method="POST" action="">
            <input type="text" name="fullname" placeholder="Full Name" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
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
            <button type="submit">Register</button>
        </form>
        <p style="text-align: center; margin-top: 10px;">Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
