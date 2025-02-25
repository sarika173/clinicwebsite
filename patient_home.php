<?php
session_start();
if (!isset($_SESSION['userData'])) {
    header("Location: login.php");
    exit();
}
echo "Welcome, " . $_SESSION['userData']['fullName'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .sidebar {
            width: 280px;
            height: 100vh;
            background: #2c3e50;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
            position: fixed;
        }
        .sidebar h2 {
            font-size: 22px;
            margin-bottom: 20px;
        }
        .sidebar a {
            text-decoration: none;
            color: white;
            padding: 12px;
            width: 90%;
            margin: 5px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 8px;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background: #34495e;
        }
        .sidebar i {
            font-size: 18px;
        }
        .main-content {
            margin-left: 200px;
            padding: 20px;
            width: 100%;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .logout {
            text-decoration: none;
            color: #e74c3c;
            font-weight: bold;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .dashboard-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s, background 0.3s;
        }
        .dashboard-card:hover {
            transform: scale(1.05);
            background: #ecf0f1;
        }
        .dashboard-card i {
            font-size: 30px;
            color: #3498db;
            margin-bottom: 10px;
        }
        .dashboard-card h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .dashboard-card p {
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Skin & Hair Clinic</h2>
        <a href="patient_profile_update.php"><i class="fa fa-user"></i> Profile</a>
        <a href="book_appointment.php"><i class="fa fa-calendar"></i> Book Appointment</a>
        <a href="patient_invoices.php"><i class="fa fa-file-invoice"></i> Invoice</a>
        <a href="patient_complaints.php"><i class="fa fa-comment"></i> Complain</a>
        <a href="old_appointments.php"><i class="fa fa-history"></i> Previous Appointments</a>
        <a href="medical_reports.php"><i class="fa fa-folder"></i> Medical Reports</a>
    </div>
    <div class="main-content">
        <div class="header">
            <h1>Welcome, <?php echo $_SESSION['userData']['fullName']; ?></h1>
            <a href="logout.php" class="logout">Logout</a>
        </div>
        <div class="dashboard-grid">
            <a href="patient_profile_update.php" class="dashboard-card">
                <i class="fa fa-user"></i>
                <h3>Profile</h3>
                <p>Update your personal details.</p>
            </a>
            <a href="book_appointment.php" class="dashboard-card">
                <i class="fa fa-calendar-check"></i>
                <h3>Book Appointment</h3>
                <p>Schedule a consultation.</p>
            </a>
            <a href="patient_invoices.php" class="dashboard-card">
                <i class="fa fa-file-invoice"></i>
                <h3>Invoices</h3>
                <p>Check and manage payments.</p>
            </a>
            <a href="patient_complaints.php" class="dashboard-card">
                <i class="fa fa-comment-dots"></i>
                <h3>Complaints</h3>
                <p>Raise concerns or feedback.</p>
            </a>
            <a href="old_appointments.php" class="dashboard-card">
                <i class="fa fa-history"></i>
                <h3>Past Appointments</h3>
                <p>Review your appointment history.</p>
            </a>
            <a href="medical_reports.php" class="dashboard-card">
                <i class="fa fa-folder-open"></i>
                <h3>Medical Reports</h3>
                <p>Access your medical records.</p>
            </a>
        </div>
    </div>
</body>
</html>
