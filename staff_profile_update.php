<?php 
session_start();
include("db.php");

if (!isset($_SESSION['staffData'])) {
    header("Location: staff_login.php");
    exit();
}

$staff_id = $_SESSION['staffData']['id'];
$staff_name = $_SESSION['staffData']['fullName'];
$staff_email = $_SESSION['staffData']['email'];
$staff_profile_pic = $_SESSION['staffData']['profile_pic'] ?? 'default.jpg';

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? $staff_name;
    $email = $_POST['email'] ?? $staff_email;
    $new_password = $_POST['password'] ?? '';

    // Handle Profile Picture Upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "uploads/staff_profiles/";
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);

        // Check file type
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png'];
        if (in_array($imageFileType, $allowed_types)) {
            move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file);
            $staff_profile_pic = $target_file;
            $_SESSION['staffData']['profile_pic'] = $staff_profile_pic;
        } else {
            echo "<script>alert('Invalid image format! Use JPG, JPEG, or PNG.');</script>";
        }
    }

    // Update Database Query
    if (!empty($new_password)) {
        $sql = "UPDATE staff SET fullName=?, email=?, password=?, profile_pic=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssssi", $name, $email, $new_password, $staff_profile_pic, $staff_id);
        } else {
            die("Error preparing query: " . $conn->error);
        }
    } else {
        $sql = "UPDATE staff SET fullName=?, email=?, profile_pic=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssi", $name, $email, $staff_profile_pic, $staff_id);
        } else {
            die("Error preparing query: " . $conn->error);
        }
    }

    if ($stmt->execute()) {
        $_SESSION['staffData']['fullName'] = $name;
        $_SESSION['staffData']['email'] = $email;
        echo "<script>alert('Profile updated successfully!'); window.location.href='staff_profile_update.php';</script>";
    } else {
        echo "<script>alert('Error updating profile!');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Profile Update</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: Arial, sans-serif; }
        .container { margin-top: 50px; }
        .card { box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        .profile-img { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #007bff; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Update Profile</h2>

    <div class="row">
        <div class="col-md-6">
            <div class="card p-3">
                <h4 class="text-center">Edit Profile</h4>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($staff_name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($staff_email); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password (Optional)</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" name="profile_pic" accept="image/*" onchange="previewImage(event)">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Profile</button>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-3 text-center">
                <h4>Profile Preview</h4>
                <img id="profilePreview" src="<?= htmlspecialchars($staff_profile_pic); ?>" class="profile-img mb-3" alt="Profile Picture">
                <h5 id="previewName"><?= htmlspecialchars($staff_name); ?></h5>
                <p id="previewEmail"><?= htmlspecialchars($staff_email); ?></p>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function(){
        var output = document.getElementById('profilePreview');
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>
