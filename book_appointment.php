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
        .content {
            flex: 1;
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        .header h1 {
            font-size: 24px;
            color: #2c3e50;
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
        <!-- Main Content -->
        <div class="content">
            <div class="header">
                <h1>Book Appointment</h1>
                <a href="logout.php">Logout</a>
            </div>
            <div class="form-section">
                <h3>Book an Appointment</h3>
                <form method="POST">
                    <label for="patient_name">Patient Name</label>
                    <input type="text" name="patient_name" id="patient_name" required>
                    
                    <label for="patient_email">Patient Email</label>
                    <input type="email" name="patient_email" id="patient_email" required>

                    <label for="appointment_date">Date</label>
                    <input type="date" name="appointment_date" id="appointment_date" required onchange="fetchTimeSlots()">
                    
                    <label for="appointment_time">Time</label>
                    <select name="appointment_time" id="appointment_time" required>
                        <!-- Time slots will be populated here -->
                    </select>
                    
                    <label for="reason">Reason for Appointment</label>
                    <textarea name="reason" id="reason" rows="4" required></textarea>
                    
                    <button type="submit">Book Appointment</button>
                </form>
            </div>
            <div class="form-section">
    <h3>Appointments</h3>
    <table>
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Patient Email</th>
                <th>Appointment Date</th>
                <th>Appointment Time</th>
                <th>Reason</th>
                <th>Action</th> <!-- Added Action column -->
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch appointments for the logged-in patient only
$query = "SELECT * FROM appointments WHERE patient_email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $userData['email']); 
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['patient_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['patient_email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['appointment_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['appointment_time']) . "</td>";
        echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
        // Add a Cancel button
        echo "<td><button onclick=\"cancelAppointment('" . $row['appointment_date'] . "', '" . $row['appointment_time'] . "')\">Cancel</button></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6'>No appointments found.</td></tr>";
}
?>
        </tbody>
    </table>
</div>

        </div>
    </div>
</body>
</html>
