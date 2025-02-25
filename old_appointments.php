<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['userData'])) {
    die("Error: User is not logged in.");
}

$userData = $_SESSION['userData'];
$patientName = $userData['fullName'];

// Fetch old appointments
$query = "SELECT appointment_date, appointment_time, reason FROM appointments WHERE patient_name = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error: " . $conn->error);
}

$stmt->bind_param("s", $patientName);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Old Appointments</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        /* Content */
        .content {
            flex: 1;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .appointment-section {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
        <!-- Main Content -->
        <div class="content">
            <div class="header">
                <h1>Old Appointments</h1>
                <a href="logout.php">Logout</a>
            </div>
            <div class="appointment-section">
                <h3>Appointment History</h3>
                <?php if (count($appointments) > 0): ?>
                <table>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Reason</th>
                    </tr>
                    <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <?php else: ?>
                <p>No previous appointments found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
