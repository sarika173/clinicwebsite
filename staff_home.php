<?php 
session_start();
if (!isset($_SESSION['staffData'])) {
    header("Location: staff_login.php");
    exit();
}
  echo "Welcome, " . $_SESSION['staffData']['fullName'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #2c3e50;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px;
        }
        .sidebar a {
            text-decoration: none;
            color: white;
            padding: 15px;
            text-align: center;
            width: 100%;
            margin-bottom: 10px;
            display: block;
        }
        .sidebar a:hover {
            background-color: #34495e;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ecf0f1;
            padding: 10px 20px;
            border-bottom: 1px solid #ccc;
        }
        .header .logout {
            text-decoration: none;
            color: #2c3e50;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        .dashboard-card {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            text-align: center;
            cursor: pointer; /* Add pointer cursor to indicate it's clickable */
        }
        .dashboard-card:hover {
            background-color: #f1f1f1; /* Add hover effect */
        }
        .dashboard-card h3 {
            margin: 10px 0;
            font-size: 18px;
        }
        .dashboard-card p {
            color: #7f8c8d;
        }
        .dashboard-card i {
            font-size: 30px;
            color: #3498db;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Skin and Hair Clinic</h2>
        <a href="inventory_management.php">Inventory Management</a>
        <a href="appointment_list.php">Appointment List</a>
        <a href="staff_profile_update.php">Profile</a>
        <a href="billing.php">Billing</a>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Welcome, <?php echo $_SESSION['staffData']['fullName']; ?></h1>
            <a href="logout.php" class="logout">Logout</a>
        </div>

        <div class="dashboard-grid">
            <a href="inventory_management.php" class="dashboard-card">
                <i class="fa fa-warehouse"></i>
                <h3>Inventory Management</h3>
                <p>Manage clinic supplies and inventory.</p>
            </a>
            <a href="appointment_list.php" class="dashboard-card">
                <i class="fa fa-calendar"></i>
                <h3>Appointment List</h3>
                <p>View and manage appointments by date, day, or month.</p>
            </a>
            <a href="staff_profile_update.php" class="dashboard-card">
                <i class="fa fa-user"></i>
                <h3>Profile</h3>
                <p>Manage your profile.</p>
            </a>
            <a href="supplier_management.php" class="dashboard-card">
                <i class="fas fa-truck"></i>
                <h3>Suppliers</h3>
                <p>Manage supplier details and orders.</p>
            </a>
            <a href="medicines_order.php" class="dashboard-card">
                <i class="fas fa-pills"></i>
                <h3>Medicine Orders</h3>
                <p>Order low-stock medicines.</p>
            </a>
            <a href="medicine_bill.php" class="dashboard-card">
                <i class="fas fa-file-invoice-dollar"></i>
                <h3>Medicine Bills</h3>
                <p>View and pay supplier bills.</p>
            </a>
        </div>
    </div>
</body>
</html>
