<?php
session_start();
include 'db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bill_id = $_POST['bill_id'];
    $payment_method = $_POST['payment_method'];

    // Validate bill exists
    $check_query = "SELECT * FROM medicine_bills WHERE bill_id='$bill_id' AND payment_status='Pending'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $update_query = "UPDATE medicine_bills SET payment_method='$payment_method', payment_status='Paid' WHERE bill_id='$bill_id'";
        if (mysqli_query($conn, $update_query)) {
            echo json_encode(['status' => 'success', 'message' => 'Payment successful!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Payment update failed!']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Bill not found or already paid!']);
    }
}
?>
