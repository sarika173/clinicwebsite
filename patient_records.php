<?php
session_start();
if (!isset($_SESSION['staffData'])) {
    header("Location: staff-login.php");
    exit();
}
include 'db.php';

// Handle filtering
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$startOfWeek = date('Y-m-d', strtotime('monday this week'));
$endOfWeek = date('Y-m-d', strtotime('sunday this week'));

$query = "SELECT * FROM appointments";
if ($filter == 'date') {
    $query .= " WHERE appointment_date = '$date'";
} elseif ($filter == 'week') {
    $query .= " WHERE appointment_date BETWEEN '$startOfWeek' AND '$endOfWeek'";
}

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Appointment List</h2>
        <form method="GET" class="mb-3">
            <label for="filter">Filter by:</label>
            <select name="filter" id="filter" class="form-select w-25" onchange="toggleDateField()">
                <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>All</option>
                <option value="date" <?= $filter == 'date' ? 'selected' : '' ?>>Specific Date</option>
                <option value="week" <?= $filter == 'week' ? 'selected' : '' ?>>This Week</option>
            </select>
            <input type="date" name="date" id="dateField" class="form-control mt-2 w-25" value="<?= $date ?>" style="display: <?= $filter == 'date' ? 'block' : 'none' ?>;">
            <button type="submit" class="btn btn-primary mt-2">Apply Filter</button>
        </form>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Appointment Date</th>
                    <th>Time</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['patient_name']; ?></td>
                        <td><?= $row['appointment_date']; ?></td>
                        <td><?= $row['appointment_time']; ?></td>
                        <td><?= $row['reason']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    
    <script>
        function toggleDateField() {
            var filter = document.getElementById('filter').value;
            var dateField = document.getElementById('dateField');
            dateField.style.display = (filter === 'date') ? 'block' : 'none';
        }
    </script>
</body>
</html>
