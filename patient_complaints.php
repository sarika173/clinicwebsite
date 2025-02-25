<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['userData'])) {
    die("Error: User is not logged in.");
}

$userData = $_SESSION['userData'];
$patientName = $userData['fullName'];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['complain_text'])) {
        $complainText = $_POST['complain_text'];

        // Insert complaint into database
        $query = "INSERT INTO complaints (patient_name, complain_text) VALUES (?, ?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            die("Error: " . $conn->error);
        }

        $stmt->bind_param("ss", $patientName, $complainText);
        if ($stmt->execute()) {
            $_SESSION['message'] = "<p class='alert alert-success'>Complaint submitted successfully.</p>";
        } else {
            $_SESSION['message'] = "<p class='alert alert-danger'>Error submitting complaint. " . $conn->error . "</p>";
        }
    } else {
        $_SESSION['message'] = "<p class='alert alert-warning'>Please fill in the complaint field.</p>";
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Complain</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .content {
            width: 100%;
            max-width: 600px;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .form-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .form-section:hover {
            transform: scale(1.02);
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        textarea {
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            width: 100%;
            resize: vertical;
        }
        button {
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="content">
        <?php
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }
        ?>
        <div class="header">
        </div>
        <div class="form-section">
            <h3>Submit a Complain</h3>
            <form method="POST">
                <label for="complain_text">Complain</label>
                <textarea name="complain_text" id="complain_text" rows="4" required></textarea>
                <button type="submit">Submit Complain</button>
            </form>
        </div>
    </div>
</body>
</html>
