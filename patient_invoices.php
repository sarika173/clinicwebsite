<?php
session_start();
require 'db.php';
require 'fpdf.php';

// Ensure User is Logged In
if (!isset($_SESSION['userData'])) {
    die("Error: Staff is not logged in.");
}

$userData = $_SESSION['userData'];
$userId = $userData['id'];
$userName = $userData['fullName'];

// Function to Generate and Download Invoice
function generate_and_download_invoice($patientName, $treatment, $amount) {
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

        function InvoiceDetails($patientName, $treatment, $amount) {
            $this->SetFont('Arial', '', 12);
            $this->Cell(0, 10, 'Patient Name: ' . $patientName, 0, 1);
            $this->Cell(0, 10, 'Treatment: ' . $treatment, 0, 1);
            $this->Cell(0, 10, 'Amount: ₹' . $amount, 0, 1);
            $this->Cell(0, 10, 'Status: Pending', 0, 1);
            $this->Cell(0, 10, 'Date: ' . date('Y-m-d H:i:s'), 0, 1);
        }
    }

    // Generate PDF
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->InvoiceDetails($patientName, $treatment, $amount);

    // Output the PDF directly to the browser for download
    $pdf->Output('D', "invoice_" . time() . ".pdf");
}

// Handle Invoice Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['patient_name']) && !empty($_POST['treatment']) && !empty($_POST['amount'])) {
        $patientName = $_POST['patient_name'];
        $treatment = $_POST['treatment'];
        $amount = $_POST['amount'];

        // Generate and download the invoice
        generate_and_download_invoice($patientName, $treatment, $amount);

        // Display a success message
        echo "<script>alert('Invoice downloaded successfully!');</script>";
    } else {
        echo "<p>Please fill in all the fields.</p>";
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
        .content {
            margin-left: 270px;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 100%;
            width: 500px;
        }
        .btn-primary {
            background-color: #1abc9c;
            border: none;
        }
        .btn-primary:hover {
            background-color: #16a085;
        }
        .form-control, .btn {
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="content">
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
                    <label class="form-label">Amount (₹)</label>
                    <input type="number" class="form-control" name="amount" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Generate Invoice</button>
            </form>
        </div>
    </div>
</body>
</html>
