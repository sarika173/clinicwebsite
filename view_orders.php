<?php  
session_start();
if (!isset($_SESSION['staffData'])) {
    header("Location: staff_login.php");
    exit();
}

include 'db.php';

// ✅ Handle status update when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // ✅ Update the status in the database
    $stmt = $conn->prepare("UPDATE medicine_orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    $stmt->close();

    // ✅ Redirect to refresh the page so status updates instantly
    header("Location: view_orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Medicine Orders</title>
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
            vertical-align: middle;
        }
        /* ✅ Status Colors */
        .status-pending { color: red !important; font-weight: bold; }
        .status-shipped { color: orange !important; font-weight: bold; }
        .status-completed { color: green !important; font-weight: bold; }
        /* ✅ Make Update Button Aligned Right */
        .update-container {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Medicine Orders</h2>
        <a href="add_order.php" class="btn btn-primary mb-3">📦 Place New Order</a>
        
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>Supplier ID</th>
                    <th>Medicine Name</th>
                    <th>Quantity</th>
                    <th>Order Date</th>
                    <th>Status</th>
                    <th>Update Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM medicine_orders ORDER BY order_date DESC";
                $result = mysqli_query($conn, $sql);

                while ($row = mysqli_fetch_assoc($result)) {
                    // ✅ Apply correct colors based on status
                    $statusClass = strtolower($row['status']) == 'pending' ? 'status-pending' :
                                  (strtolower($row['status']) == 'shipped' ? 'status-shipped' : 'status-completed');

                    echo "<tr>
                            <td>{$row['order_id']}</td>
                            <td>{$row['supplier_id']}</td>
                            <td>{$row['medicine_name']}</td>
                            <td>{$row['quantity']}</td>
                            <td>{$row['order_date']}</td>
                            <td id='status-{$row['order_id']}' class='{$statusClass}'>{$row['status']}</td>
                            <td>
                                <form method='POST' onsubmit='return updateStatus({$row['order_id']}, this)'>
                                    <div class='update-container'>
                                        <select name='status' class='form-select' style='width: 140px;'>
                                            <option value='Pending' " . ($row['status'] == 'Pending' ? 'selected' : '') . ">Pending</option>
                                            <option value='Shipped' " . ($row['status'] == 'Shipped' ? 'selected' : '') . ">Shipped</option>
                                            <option value='Completed' " . ($row['status'] == 'Completed' ? 'selected' : '') . ">Completed</option>
                                        </select>
                                        <button type='submit' class='btn btn-success btn-sm'>Update</button>
                                    </div>
                                </form>
                            </td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function updateStatus(orderId, form) {
            event.preventDefault(); // Stop form from reloading the page

            let formData = new FormData(form);

            fetch("view_orders.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(() => {
                let statusCell = document.getElementById('status-' + orderId);
                let newStatus = formData.get('status');

                // ✅ Update the status text instantly
                statusCell.textContent = newStatus;

                // ✅ Change the text color dynamically
                statusCell.className = (newStatus.toLowerCase() === "pending") ? "status-pending" :
                                       (newStatus.toLowerCase() === "shipped") ? "status-shipped" :
                                       "status-completed";
            });

            return false;
        }
    </script>

</body>
</html>
