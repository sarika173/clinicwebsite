<?php
session_start();
require 'db.php';
require 'fpdf.php';

// Ensure Staff is Logged In
if (!isset($_SESSION['staffData'])) {
    header("Location: staff_home.php"); // Redirect to login page
    exit();
}

$staffData = $_SESSION['staffData'];
$staffId = $staffData['id'];
$staffName = $staffData['fullName'];

// Function to Generate Invoice
function generate_invoice($conn, $patientName, $treatment, $amount) {
    $query = "INSERT INTO staff_billing (patient_name, treatment, amount) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        die("Error: " . $conn->error);
    }

    $stmt->bind_param("ssd", $patientName, $treatment, $amount);
    if ($stmt->execute()) {
        echo "<script>alert('Invoice generated successfully!');</script>";
    } else {
        echo "<p>Error generating invoice: " . $conn->error . "</p>";
    }
}

// Handle Invoice Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['patient_name']) && !empty($_POST['treatment']) && !empty($_POST['amount'])) {
        $patientName = $_POST['patient_name'];
        $treatment = $_POST['treatment'];
        $amount = $_POST['amount'];
        generate_invoice($conn, $patientName, $treatment, $amount);
    } else {
        echo "<p>Please fill in all the fields.</p>";
    }
}

// Retrieve Past Invoices
function get_past_invoices($conn) {
    $query = "SELECT * FROM staff_billing ORDER BY id DESC";
    $result = $conn->query($query);

    if (!$result) {
        die("Database Query Failed: " . $conn->error);
    }

    $invoices = [];
    while ($row = $result->fetch_assoc()) {
        $invoices[] = $row;
    }

    return $invoices;
}

// Generate PDF Invoice
class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Invoice', 0, 1, 'C');
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    function InvoiceDetails($invoice) {
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'Invoice ID: ' . $invoice['id'], 0, 1);
        $this->Cell(0, 10, 'Patient Name: ' . $invoice['patient_name'], 0, 1);
        $this->Cell(0, 10, 'Treatment: ' . $invoice['treatment'], 0, 1);
        $this->Cell(0, 10, 'Amount: ₹' . $invoice['amount'], 0, 1);
    }
}

$pastInvoices = get_past_invoices($conn);

// Handle PDF Download Request
if (isset($_GET['download']) && isset($_GET['invoice_id'])) {
    $invoiceId = $_GET['invoice_id'];
    foreach ($pastInvoices as $invoice) {
        if ($invoice['id'] == $invoiceId) {
            $pdf = new PDF();
            $pdf->AddPage();
            $pdf->InvoiceDetails($invoice);
            $pdf->Output('D', "invoice_{$invoice['id']}.pdf");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing & Invoice Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
        }
        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #1abc9c;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        table {
            background-color: white;
            border-radius: 5px;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        .btn-primary {
            background-color: #1abc9c;
            border: none;
        }
        .btn-primary:hover {
            background-color: #16a085;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="sidebar">
            <h2 class="text-center">Staff Dashboard</h2>
            <a href="staff_home.php">Home</a>
            <a href="appointment_list.php">Appointments</a>
            <a href="inventory_management.php">Inventory</a>
            <a href="billing.php" class="active">Billing</a>
            <a href="staff_profile_update.php">Profile</a>
        </div>
        <div class="content w-100">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="text-dark">Billing & Invoice Management</h1>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
                <div class="row">
                    <div class="col-md-5">
                        <div class="card p-4">
                            <h3>Generate Invoice</h3>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Patient Name</label>
                                    <input type="text" class="form-control" name="patient_name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Treatment</label>
                                    <input type="text" class="form-control" name="treatment" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Amount (₹)  </label>
                                    <input type="number" class="form-control" name="amount" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Generate Invoice</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card p-4">
                            <h3>Past Invoices</h3>
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Patient</th>
                                        <th>Treatment</th>
                                        <th>Amount</th>
                                        <th>Download</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pastInvoices as $invoice): ?>
                                        <tr>
                                            <td><?= $invoice['id'] ?></td>
                                            <td><?= $invoice['patient_name'] ?></td>
                                            <td><?= $invoice['treatment'] ?></td>
                                            <td>₹ <?= $invoice['amount'] ?></td>
                                            <td>
                                                <a href="?download=1&invoice_id=<?= $invoice['id'] ?>" class="btn btn-sm btn-info">Download PDF</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
