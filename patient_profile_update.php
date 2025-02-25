<?php
session_start();
require 'db.php';

// Check if user is logged in
if (!isset($_SESSION['userData'])) {
    die("Error: User is not logged in.");
}

$userData = $_SESSION['userData'];
$userId = $userData['id'];

// Fetch user details function
function fetch_user($conn, $userId) {
    $query = "SELECT * FROM registration WHERE id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Error: " . $conn->error);
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        die("Error: No user data found.");
    }

    return $user;
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle Profile Picture Update
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $imageName = $_FILES['profile_picture']['name'];
        $imageTmpName = $_FILES['profile_picture']['tmp_name'];
        $imagePath = "uploads/" . basename($imageName);

        if (move_uploaded_file($imageTmpName, $imagePath)) {
            $updatePicQuery = "UPDATE registration SET profile_pic = ? WHERE id = ?";
            $stmt = $conn->prepare($updatePicQuery);

            if (!$stmt) {
                die("Error: " . $conn->error);
            }

            $stmt->bind_param("si", $imagePath, $userId);
            $stmt->execute();
        } else {
            echo "<p>Error uploading profile picture.</p>";
        }
    }

    // Handle Username Update
    if (isset($_POST['new_username']) && $_POST['new_username'] != '') {
        $newUsername = $_POST['new_username'];
        $updateUsernameQuery = "UPDATE registration SET username = ? WHERE id = ?";
        $stmt = $conn->prepare($updateUsernameQuery);

        if (!$stmt) {
            die("Error: " . $conn->error);
        }

        $stmt->bind_param("si", $newUsername, $userId);
        $stmt->execute();
    }

    // Handle Password Update
    if (isset($_POST['old_password']) && $_POST['old_password'] != '' && isset($_POST['new_password']) && $_POST['new_password'] != '' && isset($_POST['confirm_password']) && $_POST['confirm_password'] != '') {
        $oldPassword = $_POST['old_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword !== $confirmPassword) {
            echo "<p>Passwords do not match!</p>";
        } else {
            $user = fetch_user($conn, $userId);
            // Verify old password
            if ($oldPassword == $user['password']) {
                $updatePasswordQuery = "UPDATE registration SET password = ? WHERE id = ?";
                $stmt = $conn->prepare($updatePasswordQuery);

                if (!$stmt) {
                    die("Error: " . $conn->error);
                }

                $stmt->bind_param("si", $newPassword, $userId);
                $stmt->execute();
                echo "<p>Password updated successfully.</p>";
            } else {
                echo "<p>Incorrect old password.</p>";
            }
        }
    }
}

// Fetch user details to display
$user = fetch_user($conn, $userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Update</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        /* Content */
        .content {
            flex: 1;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .profile-section {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }
        .form-section, .details-section {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            flex: 1;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        input[type="text"], input[type="password"], input[type="file"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }
        button {
            padding: 10px;
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #27ae60;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        table td:first-child {
            font-weight: bold;
        }
    </style>
</head>
<body>
        <!-- Main Content -->
        <div class="content">
            <div class="header">
                <h1>Patient Profile</h1>
                <a href="logout.php">Logout</a>
            </div>
            <div class="profile-section">
                <!-- Form Section -->
                <div class="form-section">
                    <h3>Update Profile</h3>
                    <form method="POST" enctype="multipart/form-data">
                        <label for="profile_picture">Update Profile Picture</label>
                        <input type="file" name="profile_picture" id="profile_picture">
                        
                        <h3>Change Username</h3>
                        <input type="text" name="new_username" placeholder="Enter New Username">
                        
                        <h3>Change Password</h3>
                        <input type="password" name="old_password" placeholder="Enter Old Password">
                        <input type="password" name="new_password" placeholder="Enter New Password" >
                        <input type="password" name="confirm_password" placeholder="Confirm Password">
                        
                        <button type="submit">Submit</button>
                    </form>
                </div>
                <!-- Details Section -->
                <div class="details-section">
                    <h3>Details:</h3>
                    <table>
                    <img style='height:200px; width: 200px; border-radius: 50%' src='<?php echo htmlspecialchars($user['profile_pic']); ?>'/>
                    <!-- <tr><td> </td></tr> -->
                        
                        <tr><td>Name:</td><td><?php echo htmlspecialchars($user['fullName']); ?></td></tr>
                        <tr><td>Gender:</td><td><?php echo htmlspecialchars($user['gender']); ?></td></tr>
                        <tr><td>Username:</td><td><?php echo htmlspecialchars($user['username']); ?></td></tr>
                        <tr><td>Email:</td><td><?php echo htmlspecialchars($user['email']); ?></td></tr>
                        <tr><td>Password:</td><td><?php echo htmlspecialchars($user['password']); ?></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
