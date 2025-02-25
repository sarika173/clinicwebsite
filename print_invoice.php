<?php
session_start();
if (!isset($_SESSION['staffData'])) {
    header("Location: staff-login.php");
    exit();
}
include 'db.php';

// Get invoice ID
$id = $_GET['id'];
$sql = "SELECT * FROM staff_billing WHERE id = $id";
$result = mysqli_query($conn, $sql);
$invoice = mysqli_fetch_assoc($result);
// Function to generate PDF using FPDF
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
        $this->Cell(0, 10, 'Amount: ' . $invoice['amount'], 0, 1);
        $this->Cell(0, 10, 'Status: ' . $invoice['status'], 0, 1);
        $this->Cell(0, 10, 'Created At: ' . $invoice['created_at'], 0, 1);
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $invoice['id']; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        .invoice-box {
            max-width: 600px;
            padding: 20px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            text-align: center;
        }
        .invoice-box h2 {
            color: #2c3e50;
        }
        .invoice-table {
            width: 100%;
            margin-top: 20px;
        }
        .invoice-table th, .invoice-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body onload="window.print()">

<div class="invoice-box">
    <h2>Skin and Hair Clinic</h2>
    <p><strong>Invoice ID:</strong> <?php echo $invoice['id']; ?></p>
    <p><strong>Patient Name:</strong> <?php echo $invoice['patient_name']; ?></p>
    <p><strong>Treatment:</strong> <?php echo $invoice['treatment']; ?></p>
    <p><strong>Amount:</strong> ₹<?php echo $invoice['amount']; ?></p>
    <p><strong>Date:</strong> <?php echo $invoice['date']; ?></p>
</div>

</body>
</html>
