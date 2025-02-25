<?php  
session_start();
if (!isset($_SESSION['staffData'])) {
    header("Location: staff_login.php");
    exit();
}

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supplier_id = $_POST['supplier_id'];
    $medicine_name = $_POST['medicine_name'];
    $quantity = $_POST['quantity'];
    $order_date = date("Y-m-d");
    $status = "Pending"; 

    $sql = "INSERT INTO medicine_orders (supplier_id, medicine_name, quantity, order_date, status) 
            VALUES ('$supplier_id', '$medicine_name', '$quantity', '$order_date', '$status')";
    
    if (mysqli_query($conn, $sql)) {
        $success_message = "Order placed successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Medicine Order</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Place New Medicine Order</h2>
        
        <?php if (isset($success_message)) { ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php } ?>
        <?php if (isset($error_message)) { ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php } ?>

        <form method="POST" class="card p-4">
            <div class="mb-3">
                <label class="form-label">Supplier ID</label>
                <select name="supplier_id" id="supplier_id" class="form-control" required>
                    <option value="">Select Supplier</option>
                    <?php
                    $supplierQuery = "SELECT supplier_id, supplier_name FROM suppliers";
                    $supplierResult = mysqli_query($conn, $supplierQuery);
                    while ($supplier = mysqli_fetch_assoc($supplierResult)) {
                        echo "<option value='{$supplier['supplier_id']}'>{$supplier['supplier_name']} (ID: {$supplier['supplier_id']})</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Medicine Name</label>
                <select name="medicine_name" id="medicine_name" class="form-control" required>
                    <option value="">Select a supplier first</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit Order</button>
        </form>
        <a href="view_orders.php" class="btn btn-secondary mt-3">View Orders</a>
    </div>

    <script>
        document.getElementById("supplier_id").addEventListener("change", function() {
            var supplierId = this.value;
            var medicineDropdown = document.getElementById("medicine_name");
            
            if (supplierId !== "") {
                fetch("get_medicines.php?supplier_id=" + supplierId)
                .then(response => response.json())
                .then(data => {
                    medicineDropdown.innerHTML = "";
                    data.forEach(med => {
                        medicineDropdown.innerHTML += `<option value="${med}">${med}</option>`;
                    });
                });
            } else {
                medicineDropdown.innerHTML = "<option value=''>Select a supplier first</option>";
            }
        });
    </script>
</body>
</html>
