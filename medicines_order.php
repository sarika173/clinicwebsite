<?php   
session_start();
if (!isset($_SESSION['staffData'])) {
    header("Location: staff_login.php");
    exit();
}

include 'db.php'; // Include database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { background-color: #f8f9fa; font-family: Arial, sans-serif; }
        .container { margin-top: 50px; max-width: 900px; }
        .card { border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        .table th, .table td { text-align: center; }
        .status-pending { color: red; font-weight: bold; }
        .status-shipped { color: orange; font-weight: bold; }
        .status-completed { color: green; font-weight: bold; }
    </style>
</head>
<body>

    <div class="container">
        <h2 class="text-center mb-4">Medicine Orders</h2>
        
        <div class="mt-4">
            <h4>Existing Orders</h4>
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Supplier ID</th>
                        <th>Medicine Name</th>
                        <th>Quantity</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM medicine_orders ORDER BY order_date DESC";
                    $result = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $statusClass = strtolower($row['status']) == 'pending' ? 'status-pending' :
                                          (strtolower($row['status']) == 'shipped' ? 'status-shipped' : 'status-completed');

                            echo "<tr>
                                    <td>{$row['order_id']}</td>
                                    <td>{$row['supplier_id']}</td>
                                    <td>{$row['medicine_name']}</td>
                                    <td>{$row['quantity']}</td>
                                    <td>{$row['order_date']}</td>
                                    <td class='{$statusClass}'>{$row['status']}</td>
                                    <td>
                                        <form method='POST' action='update_order_status.php'>
                                            <input type='hidden' name='order_id' value='{$row['order_id']}'>
                                            <select name='status' class='form-select'>
                                                <option value='pending' " . ($row['status'] == 'pending' ? 'selected' : '') . ">Pending</option>
                                                <option value='shipped' " . ($row['status'] == 'shipped' ? 'selected' : '') . ">Shipped</option>
                                                <option value='completed' " . ($row['status'] == 'completed' ? 'selected' : '') . ">Completed</option>
                                            </select>
                                            <button type='submit' class='btn btn-success mt-2'>Update</button>
                                        </form>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center'>No orders found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
