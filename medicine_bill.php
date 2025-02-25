<?php
session_start();
include 'db.php'; // Database connection

// Fetch shipped orders from medicine_orders & join with medicine_bills
$sql = "SELECT mb.bill_id, mo.order_id, mo.supplier_id, mo.medicine_name, mo.quantity, mb.total_amount, mb.payment_status, mb.payment_method 
        FROM medicine_bills mb
        JOIN medicine_orders mo ON mb.order_id = mo.order_id
        WHERE mo.status = 'Shipped'"; // Only show shipped orders

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Bills</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
            max-width: 1000px;
        }
        .table th, .table td {
            text-align: center;
        }
        .paid { color: green; font-weight: bold; }
        .pending { color: red; font-weight: bold; }
        .payment-section {
            display: none; /* Initially hidden */
            text-align: center;
            padding: 20px;
        }
        #qr-code {
            max-width: 250px;
            margin: 10px auto;
            display: none;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Medicine Bills</h2>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Order ID</th>
                <th>Supplier</th>
                <th>Medicine</th>
                <th>Quantity</th>
                <th>Amount</th>
                <th>Payment Status</th>
                <th>Payment Method</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $row['order_id'] ?></td>
                    <td><?= $row['supplier_id'] ?></td>
                    <td><?= $row['medicine_name'] ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td>₹<?= $row['total_amount'] ?></td>
                    <td class="<?= strtolower($row['payment_status']) == 'paid' ? 'paid' : 'pending' ?>">
                        <?= $row['payment_status'] ?>
                    </td>
                    <td><?= $row['payment_method'] ?: 'Not Paid' ?></td>
                    <td>
                        <?php if ($row['payment_status'] !== 'Paid'): ?>
                            <button class="btn btn-primary pay-btn" data-bill-id="<?= $row['bill_id'] ?>" 
                                    data-amount="<?= $row['total_amount'] ?>">Pay</button>
                        <?php else: ?>
                            <span class="badge bg-success">Paid</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Payment Popup -->
<div class="payment-section" id="payment-popup">
    <h3>Complete Payment</h3>
    <p><strong>Amount: ₹<span id="payment-amount"></span></strong></p>
    <select id="payment-method" class="form-select">
        <option value="">Select Payment Method</option>
        <option value="UPI">UPI</option>
        <option value="PhonePe">PhonePe</option>
        <option value="Card">Card</option>
        <option value="Cash">Cash</option>
    </select>
    <img id="qr-code" src="qr_code.png" alt="Scan to Pay">
    <button class="btn btn-success mt-3" id="confirm-payment">Confirm Payment</button>
</div>

<script>
document.querySelectorAll('.pay-btn').forEach(button => {
    button.addEventListener('click', function () {
        const billId = this.getAttribute('data-bill-id');
        const amount = this.getAttribute('data-amount');

        document.getElementById('payment-amount').textContent = amount;
        document.getElementById('payment-popup').style.display = 'block';
        document.getElementById('confirm-payment').setAttribute('data-bill-id', billId);
    });
});

document.getElementById('payment-method').addEventListener('change', function () {
    if (this.value === 'UPI' || this.value === 'PhonePe') {
        document.getElementById('qr-code').style.display = 'block';
    } else {
        document.getElementById('qr-code').style.display = 'none';
    }
});

document.getElementById('confirm-payment').addEventListener('click', function () {
    const billId = this.getAttribute('data-bill-id');
    const method = document.getElementById('payment-method').value;

    if (!method) {
        alert('Please select a payment method');
        return;
    }

    fetch('update_payment.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'bill_id=' + billId + '&payment_method=' + method
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            window.location.reload(); // Refresh to update status
        } else {
            alert('Payment failed');
        }
    });
});
</script>

</body>
</html>
