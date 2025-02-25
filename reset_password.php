<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_SESSION['email'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword != $confirmPassword) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        
        // Update the password in the database
        $sql = "UPDATE registration SET password = '$hashedPassword', otp = NULL, otp_expiry = NULL WHERE email = '$email'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Password reset successful!');</script>";
            echo "<script>window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Error resetting password.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
           body {
    font-family: Arial, sans-serif;
    background-image: url('bg 5.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    margin: 0;
    padding: 0;
}

.container {
    width: 400px;
    margin: 100px auto;
    background: rgba(255, 255, 255, 0.9);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.title {
    text-align: center;
    margin-bottom: 20px;
    color:rgb(179, 69, 223);
}

.form_group {
    margin: 15px 0;
}

.form_group label {
    display: block;
    margin-bottom: 5px;
}

.input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.btn {
    width: 100%;
    padding: 10px;
    background-color: rgb(179, 69, 223);
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.btn:hover {
    background-color: rgba(168, 48, 216, 0.89);
}
        </style>
</head>
<body>
    <div class="container">
        <h2 class="title">Reset Password</h2>
        <form method="POST" action="">
            <div class="form_group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" class="input" placeholder="New Password" required>
            </div>
            <div class="form_group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="input" placeholder="Confirm Password" required>
            </div>
            <div class="form_group">
                <button type="submit" name="reset_password" class="btn">Reset Password</button>
            </div>
        </form>
    </div>
</body>
</html>
