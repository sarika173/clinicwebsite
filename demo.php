<?php
// Database connection
require 'db.php';

// Add new appointment record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_appointment'])) {
    $date = $_POST['appointment_date'];
    $patient_name = $_POST['patient_name'];
    $doctor_name = $_POST['doctor_name'];
    $conn->query("INSERT INTO appointments (appointment_date, patient_name, doctor_name) VALUES ('$date', '$patient_name', '$doctor_name')");
}

// Add new patient record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_patient'])) {
    $name = $_POST['patient_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $contact_info = $_POST['contact_info'];
    $conn->query("INSERT INTO patients (name, age, gender, contact_info) VALUES ('$name', '$age', '$gender', '$contact_info')");
}

// Add new treatment/prescription record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_treatment'])) {
    $patient_id = $_POST['patient_id'];
    $treatment_date = $_POST['treatment_date'];
    $prescription = $_POST['prescription'];
    $conn->query("INSERT INTO treatments (patient_id, treatment_date, prescription) VALUES ('$patient_id', '$treatment_date', '$prescription')");
}

// Add new billing record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_billing'])) {
    $patient_id = $_POST['patient_id'];
    $billing_date = $_POST['billing_date'];
    $amount = $_POST['amount'];
    $conn->query("INSERT INTO billing (patient_id, billing_date, amount) VALUES ('$patient_id', '$billing_date', '$amount')");
}

// Fetch records for different sections
$appointment_records = $conn->query("SELECT * FROM appointments");
$patient_records = $conn->query("SELECT * FROM patients");
$treatment_records = $conn->query("SELECT * FROM treatments");
$billing_records = $conn->query("SELECT * FROM billing");

// Determine which section to display
$section = isset($_GET['section']) ? $_GET['section'] : 'overview'; // Default to overview if no section is selected
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color:lightblue;
            color: #333;
        }

        .sidebar {
            width: 250px;
            background-color: #007BFF;
            color: white;
            height: 100vh;
            position: fixed;
            padding: 20px;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            background-color: #007BFF;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 5px;
            background-color: #0056b3;
        }

        .sidebar a:hover {
            background-color: #003f7f;
        }

        .content {
            margin-left: 270px;
            padding: 20px;
        }

        h1 {
            margin-bottom: 20px;
            text-align: center;
        }

        h2 {
            text-align: center;
            color:white;
            background-color:green;
            margin-bottom: 20px;
        }

        .form-container {
            margin-bottom: 30px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        .form-container input[type="date"],
        .form-container input[type="number"],
        .form-container input[type="text"],
        .form-container button {
            padding: 10px;
            margin-bottom: 15px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-container button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #007BFF;
            color: white;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .delete-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
            text-decoration: none;
        }

        .delete-btn {
            background-color: #dc3545;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        .overview-card {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 18%;
            text-align: center;
            margin: 10px 1%;
        }

        .card h3 {
            margin-bottom: 10px;
            font-size: 22px;
            color: #007BFF;
        }

        .card p {
            font-size: 26px;
            font-weight: bold;
            margin: 0;
            color: #333;
        }

    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Clinic Dashboard</h2>
        <a href="?section=overview">Overview</a>
        <a href="?section=appointment_management">Appointment Management</a>
        <a href="?section=patient_management">Patient Management</a>
        <a href="?section=treatment_management">Treatment Management</a>
        <a href="?section=billing_management">Billing Management</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
        <?php if ($section === 'overview'): ?>
            <h1>Overview</h1>
            <div class="overview-card">
                <div class="card">
                    <h3>Total Patients</h3>
                    <p>150</p>
                </div>
                <div class="card">
                    <h3>Total Appointments</h3>
                    <p>200</p>
                </div>
                <div class="card">
                    <h3>Total Treatments</h3>
                    <p>500</p>
                </div>
                <div class="card">
                    <h3>Total Billing</h3>
                    <p>$5000</p>
                </div>
            </div>
        <?php elseif ($section === 'appointment_management'): ?>
            <h1>Appointment Management</h1>

            <h2>Add New Appointment</h2>
            <div class="form-container">
                <h3>Add Appointment</h3>
                <form method="POST">
                    <input type="date" name="appointment_date" required>
                    <input type="text" name="patient_name" placeholder="Enter Patient Name" required>
                    <input type="text" name="doctor_name" placeholder="Enter Doctor Name" required>
                    <button type="submit" name="add_appointment">Add Appointment</button>
                </form>
            </div>

            <table>
                <tr>
                    <th>Date</th>
                    <th>Patient Name</th>
                    <th>Doctor Name</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $appointment_records->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['appointment_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                        <td class="action-buttons">
                            <a href="?section=appointment_management&delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this appointment?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

        <?php elseif ($section === 'patient_management'): ?>
            <h1>Patient Management</h1>

            <h2>Add New Patient</h2>
            <div class="form-container">
                <h3>Add Patient</h3>
                <form method="POST">
                    <input type="text" name="patient_name" placeholder="Enter Patient Name" required>
                    <input type="number" name="age" placeholder="Enter Age" required>
                    <input type="text" name="gender" placeholder="Enter Gender" required>
                    <input type="text" name="contact_info" placeholder="Enter Contact Info" required>
                    <button type="submit" name="add_patient">Add Patient</button>
                </form>
            </div>

            <table>
                <tr>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Contact Info</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $patient_records->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['age']); ?></td>
                        <td><?php echo htmlspecialchars($row['gender']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact_info']); ?></td>
                        <td class="action-buttons">
                            <a href="?section=patient_management&delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this patient?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

        <?php elseif ($section === 'treatment_management'): ?>
            <h1>Treatment Management</h1>

            <h2>Add New Treatment</h2>
            <div class="form-container">
                <h3>Add Treatment</h3>
                <form method="POST">
                    <input type="number" name="patient_id" placeholder="Enter Patient ID" required>
                    <input type="date" name="treatment_date" required>
                    <input type="text" name="prescription" placeholder="Enter Prescription" required>
                    <button type="submit" name="add_treatment">Add Treatment</button>
                </form>
            </div>

            <table>
                <tr>
                    <th>Patient ID</th>
                    <th>Treatment Date</th>
                    <th>Prescription</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $treatment_records->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['patient_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['treatment_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['prescription']); ?></td>
                        <td class="action-buttons">
                            <a href="?section=treatment_management&delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this treatment?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>

        <?php elseif ($section === 'billing_management'): ?>
            <h1>Billing Management</h1>

            <h2>Add New Billing</h2>
            <div class="form-container">
                <h3>Add Billing</h3>
                <form method="POST">
                    <input type="number" name="patient_id" placeholder="Enter Patient ID" required>
                    <input type="date" name="billing_date" required>
                    <input type="number" name="amount" placeholder="Enter Amount" required>
                    <button type="submit" name="add_billing">Add Billing</button>
                </form>
            </div>

            <table>
                <tr>
                    <th>Patient ID</th>
                    <th>Billing Date</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $billing_records->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['patient_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['billing_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['amount']); ?></td>
                        <td class="action-buttons">
                            <a href="?section=billing_management&delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this billing record?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
