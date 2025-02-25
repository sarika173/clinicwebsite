<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<div class="sidebar">
    <h2>Skin & Hair Clinic</h2>
    <a href="patient_profile.php"><i class="fas fa-user"></i> Profile</a>
    <a href="book_appointment.php"><i class="fas fa-calendar-check"></i> Book Appointment</a>
    <a href="patient_invoices.php"><i class="fas fa-file-invoice"></i> Invoice</a>
    <a href="patient_complaints.php" class="active"><i class="fas fa-comments"></i> Complaint</a>
    <a href="old_appointments.php"><i class="fas fa-history"></i> Previous Appointments</a>
    <a href="medical_reports.php"><i class="fas fa-folder-open"></i> Medical Reports</a>
</div>
<?php echo "Sidebar loaded"; ?>


<style>
    .sidebar {
        width: 250px;
        background-color: #2c3e50;
        height: 100vh;
        color: white;
        padding-top: 20px;
        position: fixed;
        left: 0;
        top: 0;
    }
        .sidebar h2 {
    text-align: center;
    margin-bottom: 30px;
}
.sidebar a {
    text-decoration: none;
    display: block;
    padding: 10px 20px;
    color: white;
    border-bottom: 1px solid #34495e;
}
.sidebar a.active {
    background-color: #1abc9c;
}
.sidebar a:hover {
    background-color: #16a085;
}
<!-- Include FontAwesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">