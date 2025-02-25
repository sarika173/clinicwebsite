<?php
session_start();
require 'db.php';

// Ensure User is Logged In
if (!isset($_SESSION['userData'])) {
    die("Error: Staff is not logged in.");
}

$userData = $_SESSION['userData'];
$userId = $userData['id'];
$userName = $userData['fullName'];

// File upload handling
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['medical_report']) && $_FILES['medical_report']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/medical_reports/'; // Directory to save uploaded reports
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directory if not exists
        }

        $fileName = basename($_FILES['medical_report']['name']);
        $uploadPath = $uploadDir . time() . '_' . $fileName;

        // Move uploaded file to the designated folder
        if (move_uploaded_file($_FILES['medical_report']['tmp_name'], $uploadPath)) {
            echo "<script>alert('Medical report uploaded successfully!');</script>";
        } else {
            echo "<script>alert('Error: Failed to upload the report.');</script>";
        }
    } else {
        echo "<script>alert('Please choose a valid file to upload.');</script>";
    }
}

// Handle file deletion
if (isset($_GET['delete'])) {
    $fileToDelete = 'uploads/medical_reports/' . $_GET['delete'];
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
        echo "<script>alert('Medical report deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error: File not found.');</script>";
    }
}

// Fetch uploaded reports
function fetch_uploaded_reports($dir) {
    $files = array();
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != "." && $file != "..") {
                    $files[] = $file;
                }
            }
            closedir($dh);
        }
    }
    return $files;
}

$uploadedReports = fetch_uploaded_reports('uploads/medical_reports/');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Medical Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
        }
        .container-wrapper {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 800px;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #1abc9c;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #16a085;
        }
        table {
            border-radius: 8px;
            background-color: white;
            width: 100%;
        }
        th {
            background-color: #2c3e50;
            color: white;
            text-align: center;
        }
        td {
            text-align: left;
            padding: 8px 12px;
        }
        .download-link {
            text-decoration: none;
            color: #1abc9c;
        }
        .delete-link {
            text-decoration: none;
            color: #e74c3c;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container-wrapper">
        <div class="card">
            <h3>Upload Medical Report</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Choose Medical Report</label>
                    <input type="file" class="form-control" name="medical_report" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Upload Report</button>
            </form>
        </div>

        <div class="card">
            <h3>Your Uploaded Reports</h3>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Report Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($uploadedReports)) {
                        foreach ($uploadedReports as $report) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($report) . '</td>';
                            echo '<td>';
                            echo '<a href="uploads/medical_reports/' . htmlspecialchars($report) . '" class="download-link" download>Download</a>';
                            echo '<a href="?delete=' . htmlspecialchars($report) . '" class="delete-link" onclick="return confirm(\'Are you sure you want to delete this report?\')">Delete</a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="2">No reports uploaded yet.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
