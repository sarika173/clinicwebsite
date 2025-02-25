<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $otp = generateOTP();
    $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    // Save OTP and its expiry in the database
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
<html>
<head>
    <title>Forgot Password</title>
    <style>
    body {
    font-family: Arial, sans-serif;
    background-image: url('bg 5.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    margin: 0;
    padding: 0;
    height: 600px;
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
        <h2 class="title">Forgot Password</h2>
        <form method="POST" action="">
            <div class="form_group">
                <label for="email">Email ID</label>
                <input type="email" id="email" name="email" class="input" placeholder="Email ID" required>
            </div>
            <div class="form_group">
                <button type="submit" name="send_otp" class="btn">Send OTP</button>
            </div>
        </form>
    </div>
</body>
</html>
