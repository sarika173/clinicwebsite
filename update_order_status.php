<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    $sql = "UPDATE medicine_orders SET status='$status' WHERE order_id='$order_id'";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Order status updated successfully'); window.location.href='medicines_order.php';</script>";
    } else {
        echo "<script>alert('Failed to update order status'); window.location.href='medicines_order.php';</script>";
    }
}
?>
