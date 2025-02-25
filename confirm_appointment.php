<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Database connection
include "db.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointmentId = $_POST["id"];
    $patientEmail = $_POST["email"];

    // Update appointment status to "Confirmed"
    $updateQuery = "UPDATE appointments SET status='Confirmed' WHERE id='$appointmentId'";
    
    if ($conn->query($updateQuery) === TRUE) {
        
        // Send email notification
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'skinandhairclinic17@gmail.com'; // Replace with your email
            $mail->Password = 'icjo rqha sqkv ybvl'; // Replace with your app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('your-skinandhairclinic17@gmail.com', 'Skin & Hair Clinic');
            $mail->addAddress($patientEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Appointment Confirmation';
            $mail->Body = "
                <p>Dear Patient,</p>
                <p>Your appointment has been <strong>confirmed</strong>.</p>
                <p>Thank you for choosing our clinic.</p>
                <p>Best Regards,<br><b>Skin & Hair Clinic</b></p>
            ";

            if ($mail->send()) {
                echo "Appointment confirmed & email sent!";
            } else {
                echo "Appointment confirmed but email failed: " . $mail->ErrorInfo;
            }
        } catch (Exception $e) {
            echo "Error sending email: " . $mail->ErrorInfo;
        }
    } else {
        echo "Failed to update appointment.";
    }
}
$conn->close();
?>
