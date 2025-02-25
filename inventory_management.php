<?php
session_start();
if (!isset($_SESSION['staffData'])) {
    header("Location: staff-login.php");
    exit();
}
include 'db.php';

// Handle medicine addition
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['medicine_name'];
    $quantity = $_POST['quantity'];
    $expiry_date = $_POST['expiry_date'];
    
    $sql = "INSERT INTO inventory (medicine_name, quantity, expiry_date) VALUES ('$name', '$quantity', '$expiry_date')";
    mysqli_query($conn, $sql);
}

// Fetch all medicines
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT * FROM inventory WHERE medicine_name LIKE '%$search%'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .low-stock {
            color: red;
        }
    </style>
    <script>
        function checkStock() {
            <?php
            $lowStockQuery = "SELECT * FROM inventory WHERE quantity < 5";
            $lowStockResult = mysqli_query($conn, $lowStockQuery);
            while ($row = mysqli_fetch_assoc($lowStockResult)) {
                echo "alert('Low stock alert: " . $row['medicine_name'] . " has only " . $row['quantity'] . " left!');";
            }
            ?>
        }

        function displayMedicines() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_medicines.php', true);
            xhr.onload = function() {
                if (this.status == 200) {
                    document.getElementById('medicine-list').innerHTML = this.responseText;
                }
            }
            xhr.send();
        }
    </script>
</head>
<body onload="checkStock()">
    <div class="container mt-5">
        <h2>Inventory Management</h2>
        <form class="d-flex mb-3" method="GET">
            <input class="form-control me-2" type="text" name="search" placeholder="Search medicines...">
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
        
        <form method="POST" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="medicine_name" spellcheck="true" placeholder="Medicine Name" required>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" name="quantity" placeholder="Quantity" required>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" name="expiry_date" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success">Add Medicine</button>
                </div>
            </div>
        </form>
        
        <button class="btn btn-info mb-3" onclick="displayMedicines()">Show All Medicines</button>
        
        <div id="medicine-list">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Expiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr class="<?php echo $row['quantity'] < 5 ? 'low-stock' : ''; ?>">
                            <td><?php echo $row['medicine_name']; ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td><?php echo $row['expiry_date']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
