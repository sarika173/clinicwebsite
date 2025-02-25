<?php
include 'db.php';

$supplier_id = $_GET['supplier_id'];
$medicines = [];

$query = "SELECT supplied_medicines FROM suppliers WHERE supplier_id = '$supplier_id'";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    $medicines = explode(",", $row['supplied_medicines']); 
}

echo json_encode($medicines);
?>
