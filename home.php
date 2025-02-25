<?php
session_start();

$userData = $_SESSION['userData']

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Skin and Hair Clinic</title>
  <link rel="stylesheet" href="style.css"></head>
<body >
  
  <div class="navbar">
    <div class="logo">Skin & Hair Clinic</div>
    <div>
      <a href="#home">Home</a>
      <a href="#appointment">Appointments</a>
      <a href="#inventory">Inventory</a>
      <a href="#about">About Us</a>
      <a href="#contact">Contact</a>
      
    </div>
  </div>

  <div style="padding: 20px;">
    <h1>Hello, <?php echo $userData['fullName']."<br>" ?> Welcome to Skin & Hair Clinic</h1>
    <p>Your one-stop solution for all skin and hair care needs.</p>
  </div>
</body>
</html>
