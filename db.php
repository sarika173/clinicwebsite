<?php
$servername = "localhost";  // Change if your database is hosted elsewhere
$username = "root";         // Change to your database username
$password = "";             // Change to your database password
$dbname = "clinicdatabse"; // Change to your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
