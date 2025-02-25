<?php
require 'db.php';

if (isset($_GET['date'], $_GET['time'], $_GET['reason'])) {
    $appointmentDate = $_GET['date'];
    $appointmentTime = $_GET['time'];
    $cancelReason = $_GET['reason'];

    $query = "DELETE FROM appointments WHERE appointment_date = ? AND appointment_time = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ss", $appointmentDate, $appointmentTime);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to cancel appointment."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request parameters."]);
}
?>
