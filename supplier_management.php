<?php 
session_start();
include("db.php");

if (!isset($_SESSION['staffData'])) {
    header("Location: staff_login.php");
    exit();
}

// Handle Add Supplier
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_supplier'])) {
    $name = $_POST['supplier_name'] ?? '';
    $medicines = $_POST['supplied_medicines'] ?? '';
    $contact = $_POST['contact_info'] ?? '';

    if (!empty($name) && !empty($medicines) && !empty($contact)) {
        $stmt = $conn->prepare("INSERT INTO suppliers (supplier_name, supplied_medicines, contact_info, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $name, $medicines, $contact);
        $stmt->execute();
        $stmt->close();
        header("Location: supplier_management.php");
        exit();
    } else {
        echo "<script>alert('All fields are required!');</script>";
    }
}

// Handle Delete Supplier
if (isset($_GET['delete'])) {
    $supplier_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM suppliers WHERE supplier_id = ?");
    $stmt->bind_param("i", $supplier_id);
    $stmt->execute();
    $stmt->close();
    header("Location: supplier_management.php");
    exit();
}

// Fetch Suppliers List
$suppliers = $conn->query("SELECT * FROM suppliers ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Supplier Management</h2>

    <div class="row">
        <!-- Add Supplier Form (Left) -->
        <div class="col-md-4">
            <div class="card p-3">
                <h4 class="text-center">Add Supplier</h4>
                <form method="POST">
                    <div class="mb-2">
                        <label class="form-label">Supplier Name</label>
                        <input type="text" class="form-control" name="supplier_name" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Supplied Medicines</label>
                        <textarea class="form-control" name="supplied_medicines" required></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Contact Info</label>
                        <input type="text" class="form-control" name="contact_info" required>
                    </div>
                    <button type="submit" name="add_supplier" class="btn btn-primary w-100">Add Supplier</button>
                </form>
            </div>
        </div>

        <!-- Supplier List (Right) -->
        <div class="col-md-8">
            <div class="card p-3">
                <h4 class="text-center">Supplier List</h4>
                <table class="table table-bordered mt-3">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Supplier Name</th>
                            <th>Supplied Medicines</th>
                            <th>Contact Info</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $suppliers->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['supplier_id']; ?></td>
                                <td><?php echo $row['supplier_name']; ?></td>
                                <td><?php echo $row['supplied_medicines']; ?></td>
                                <td><?php echo $row['contact_info']; ?></td>
                                <td>
                                    <a href="?delete=<?php echo $row['supplier_id']; ?>" class="btn btn-delete btn-sm" onclick="return confirm('Are you sure you want to delete this supplier?');">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if ($suppliers->num_rows == 0) { ?>
                            <tr>
                                <td colspan="5" class="text-center">No suppliers found.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</body>
</html>
