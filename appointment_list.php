<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 40px;
        }
        .table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        th {
            background-color: #007bff;
            color: white;
            text-align: center;
        }
        .btn-confirm {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-confirm:hover {
            background-color: #218838;
        }
        .confirmed {
            color: #28a745;
            font-weight: bold;
        }
        .filter-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .filter-section select, .filter-section input {
            padding: 5px;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Appointment List</h2>

    <!-- Filter Section -->
    <div class="filter-section">
        <select id="filter" class="form-select w-25">
            <option value="all">All Appointments</option>
            <option value="today">Today's Appointments</option>
            <option value="week">This Week's Appointments</option>
        </select>

        <!-- Date Picker for Specific Date -->
        <input type="date" id="specificDate" class="form-control w-25">
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Appointment Date</th>
                    <th>Time</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="appointmentData">
                <?php
                include "db.php";

                $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
                $specificDate = isset($_GET['date']) ? $_GET['date'] : '';
                
                $today = date('Y-m-d');
                $weekStart = date('Y-m-d', strtotime("monday this week"));
                $weekEnd = date('Y-m-d', strtotime("sunday this week"));

                $query = "SELECT * FROM appointments";

                if ($filter == "today") {
                    $query .= " WHERE appointment_date = '$today'";
                } elseif ($filter == "week") {
                    $query .= " WHERE appointment_date BETWEEN '$weekStart' AND '$weekEnd'";
                } elseif (!empty($specificDate)) {
                    $query .= " WHERE appointment_date = '$specificDate'";
                }

                $result = $conn->query($query);

                while ($row = $result->fetch_assoc()) {
                    echo '<tr>
                        <td>' . $row["patient_name"] . '</td>
                        <td>' . $row["appointment_date"] . '</td>
                        <td>' . $row["appointment_time"] . '</td>
                        <td>' . $row["reason"] . '</td>
                        <td class="text-center">';

                    if ($row["status"] == "Pending") {
                        echo '<span class="badge bg-warning">Pending</span>';
                    } else {
                        echo '<span class="badge bg-success">Confirmed</span>';
                    }

                    echo '</td>
                        <td class="text-center">';

                    if ($row["status"] == "Pending") {
                        echo '<button class="btn-confirm" data-id="' . $row["id"] . '" data-email="' . $row["patient_email"] . '">Confirm</button>';
                    } else {
                        echo '<span class="confirmed">Confirmed</span>';
                    }

                    echo '</td></tr>';
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function(){
    // Filter change event
    $("#filter").change(function() {
        let filterType = $(this).val();
        window.location.href = "appointment_list.php?filter=" + filterType;
    });

    // Date filter event
    $("#specificDate").change(function() {
        let selectedDate = $(this).val();
        window.location.href = "appointment_list.php?date=" + selectedDate;
    });

    // Confirm appointment functionality
    $(".btn-confirm").click(function(){
        var appointmentId = $(this).data("id");
        var patientEmail = $(this).data("email");

        $.ajax({
            url: "confirm_appointment.php",
            type: "POST",
            data: { id: appointmentId, email: patientEmail },
            success: function(response) {
                alert(response);
                location.reload();
            }
        });
    });
});
</script>

</body>
</html>
