<?php 
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['userData'])) {
    die("Error: User is not logged in.");
}

$userData = $_SESSION['userData'];
$userId = $userData['id'];

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

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['appointment_date']) && !empty($_POST['appointment_time']) && !empty($_POST['reason']) && !empty($_POST['patient_name']) && !empty($_POST['patient_email'])) {
        $appointmentDate = $_POST['appointment_date'];
        $appointmentTime = $_POST['appointment_time'];
        $reason = $_POST['reason'];
        $patientName = $_POST['patient_name'];
        $patientEmail = $_POST['patient_email'];

        if (!is_time_slot_booked($conn, $appointmentDate, $appointmentTime)) {
            $query = "INSERT INTO appointments (appointment_date, appointment_time, reason, patient_name, patient_email) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                die("Error: " . $conn->error);
            }

            $stmt->bind_param("sssss", $appointmentDate, $appointmentTime, $reason, $patientName, $patientEmail);
            if ($stmt->execute()) {
                echo "<p>Appointment booked successfully.</p>";
            } else {
                echo "<p>Error booking appointment: " . $conn->error . "</p>";
            }
        } else {
            echo "<p>Selected time slot is already booked.</p>";
        }
    } else {
        echo "<p>Please fill in all the fields.</p>";
    }
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            padding-top: 20px;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar a {
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            color: white;
            border-bottom: 1px solid #34495e;
            font-size: 16px;
        }
        .sidebar a.active {
            background-color: #1abc9c;
        }
        .sidebar a:hover {
            background-color: #16a085;
        }
        .content {
            flex: 1;
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 24px;
            color: #2c3e50;
        }
        .header a {
            padding: 10px 20px;
            background-color: #ff5e57;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        .header a:hover {
            background-color: #e74c3c;
        }
        .form-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        input[type="date"], textarea, select, input[type="email"], input[type="text"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            margin-bottom: 10px;
        }
        button {
            padding: 10px;
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #27ae60;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }
        th {
            background-color: #2c3e50;
            color: white;
        }
    </style>
    <script>
        function fetchTimeSlots() {
            const appointmentDate = document.getElementById('appointment_date').value;
            const currentDate = new Date().toISOString().split('T')[0];
            
            if (appointmentDate < currentDate) {
                document.getElementById('message').textContent = 'You cannot book an appointment for past dates.';
                document.getElementById('appointment_time').innerHTML = '';
                return;
            } else {
                document.getElementById('message').textContent = '';
            }

            fetch(`get_time_slot.php?date=${appointmentDate}`)
                .then(response => response.json())
                .then(data => {
                    const timeSelect = document.getElementById('appointment_time');
                    timeSelect.innerHTML = '';

                    data.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot.time;
                        option.textContent = slot.time;
                        option.disabled = slot.booked;
                        timeSelect.appendChild(option);
                    });
                });
        }
    
        function cancelAppointment(appointmentDate, appointmentTime) {
            const cancelReason = prompt("Please provide a reason for canceling the appointment:");
            if (cancelReason) {
                fetch(`cancel_appointment.php?date=${appointmentDate}&time=${appointmentTime}&reason=${encodeURIComponent(cancelReason)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Appointment canceled successfully.");
                        location.reload();
                    } else {
                        alert("Error canceling appointment: " + data.message);
                    }
                })
                .catch(error => alert("An error occurred: " + error.message));
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Dashboard</h2>
            <a href="patient_profile_update.php">Profile</a>
            <a href="book_appointment.php" class="active">Book Appointment</a>
            <a href="patient_invoices.php">Invoice</a>
            <a href="patient_complaints.php">Complain</a>
            <a href="old_appointments.php">Old Appointments</a>
        </div>
        <!-- Main Content -->
        <div class="content">
            <div class="header">
                <h1>Book Appointment</h1>
                <div>
                    <a href="logout.php">Logout</a>
                    <a href="book_appointment.php">Book Appointment</a>
                </div>
            </div>
            <div class="form-section">
                <h3>Book an Appointment</h3>
                <div id="message" class="message"></div>
                <form method="POST">
                    <label for="patient_name">Patient Name</label>
                    <input type="text" name="patient_name" id="patient_name" required>
                    
                    <label for="