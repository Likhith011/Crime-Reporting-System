<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- CRITICAL FOR PRODUCTION: Set to 0 to prevent debugging output interfering with AJAX ---
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL); // Error reporting level for logging, not necessarily display

// Adjust these paths if your PHPMailer library is in a different location
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';

header('Content-Type: application/json'); // Crucial for AJAX response

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
        exit();
    }

    $otp = rand(100000, 999999);
    $_SESSION['reg_otp'] = $otp;
    $_SESSION['reg_otp_email'] = $email; // Store email with OTP for verification later
    $_SESSION['otp_expiry'] = time() + (5 * 60); // OTP valid for 5 minutes

    $mail = new PHPMailer(true); // 'true' enables exceptions, which helps with debugging

    try {
        // --- THE KEY FIX: Set to 0 to disable debug output for production/AJAX calls ---
        $mail->SMTPDebug = 0; // Disable verbose debug output
        // If you need to debug in production without breaking AJAX, use this:
        // $mail->Debugoutput = function($str, $level) { error_log("PHPMailer Debug: " . $str); };

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'onlinecrimeportal@gmail.com'; // Your Gmail address
        $mail->Password = 'wqme eref jtxp wsmf'; // App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS encryption
        $mail->Port = 587; // Port for STARTTLS

        $mail->setFrom('onlinecrimeportal@gmail.com', 'Crime Portal');
        $mail->addAddress($email); // Add recipient email
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'Your OTP for Registration - Crime Portal';
        $mail->Body = "Hi,<br><br>Your OTP for registration is: <b>$otp</b><br><br>This OTP is valid for 5 minutes.<br><br>If you didn’t request this, please ignore this email.";

        $mail->send();
        echo json_encode(['success' => true, 'message' => 'OTP sent to your email.']);
    } catch (Exception $e) {
        // Log the detailed error from PHPMailer for debugging (will go to server's error log)
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo . ' (Check server error logs for details).']);
    }
    exit(); // Always exit after sending JSON response
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']); // For requests not POST or missing email
?>