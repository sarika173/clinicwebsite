<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_SESSION['email'];
    $otp = $_POST['otp'];

    // Check if OTP is valid and not expired
    $sql = "SELECT otp, otp_expiry FROM registration WHERE email = '$email'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    if ($row['otp'] == $otp && strtotime($row['otp_expiry']) > time()) {
        echo "<script>alert('OTP verified!');</script>";
        echo "<script>window.location.href='reset_password.php';</script>";
    } else {
        echo "<script>alert('Invalid or expired OTP.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP</title>
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
        <h2 class="title">Verify OTP</h2>
        <form method="POST" action="">
            <div class="form_group">
                <label for="otp">OTP</label>
                <input type="text" id="otp" name="otp" class="input" placeholder="Enter OTP" required>
            </div>
            <div class="form_group">
                <button type="submit" name="verify_otp" class="btn">Verify OTP</button>
            </div>
        </form>
    </div>
</body>
</html>
