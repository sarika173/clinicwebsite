<?php
require 'db.php';

if (isset($_GET['date'])) {
    $appointmentDate = $_GET['date'];

    // Function to check if a time slot is booked
    function is_time_slot_booked($conn, $appointmentDate, $appointmentTime) {
        $query = "SELECT COUNT(*) as count FROM appointments WHERE appointment_date = ? AND appointment_time = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            die("Error: " . $conn->error);
        }

        $stmt->bind_param("ss", $appointmentDate, $appointmentTime);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row['count'] > 0;
    }

    // Function to generate time slots
    function generate_time_slots($conn, $appointmentDate) {
        $startTime = strtotime("09:00");
        $endTime = strtotime("17:00");
        $interval = 300; // 5 minutes interval
        $timeSlots = [];

        for ($time = $startTime; $time <= $endTime; $time += $interval) {
            $formattedTime = date("H:i", $time);
            $timeSlots[] = [
                'time' => $formattedTime,
                'booked' => is_time_slot_booked($conn, $appointmentDate, $formattedTime)
            ];
        }

        return $timeSlots;
    }

    $timeSlots = generate_time_slots($conn, $appointmentDate);
    echo json_encode($timeSlots);
}
