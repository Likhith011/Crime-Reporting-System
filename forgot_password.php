<?php
session_start();
$message = "";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if (isset($_POST['submit'])) {
    $conn = mysqli_connect("localhost", "root", "", "crime_portal");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $email = $_POST['email'];

    // Check if email exists in the database
    $query = "SELECT u_id FROM user WHERE u_id='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Insert token into the database
        $insert = "INSERT INTO password_reset (email, token, expiry) VALUES ('$email', '$token', '$expiry')";
        mysqli_query($conn, $insert);

        // Send reset email
        require 'PHPMailer/src/PHPMailer.php';
        require 'PHPMailer/src/SMTP.php';
        require 'PHPMailer/src/Exception.php';

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'onlinecrimeportal@gmail.com';
        $mail->Password = 'wqme eref jtxp wsmf'; // App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('onlinecrimeportal@gmail.com', 'Crime Portal');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body = "<p>Click the link below to reset your password:</p>" .
                      "<a href='http://localhost/Crime-Reporting-System/reset_password.php?token=$token'>Reset Password</a>";

        if ($mail->send()) {
            $message = "Password reset link has been sent to your email.";
        } else {
            $message = "Failed to send email. Please try again.";
        }
    } else {
        $message = "Email not found.";
    }
}
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Forgot Password</h2>
    <?php if ($message != ""): ?>
        <div class="alert alert-info text-center"> <?= $message ?> </div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label for="email" class="form-label">Enter your email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Send Reset Link</button>
    </form>
</div>
</body>
</html>
