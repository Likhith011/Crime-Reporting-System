<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
    $user_otp = trim($_POST['otp']);

    // Check if OTP is stored in session and not expired
    if (!isset($_SESSION['reg_otp']) || !isset($_SESSION['otp_expiry']) || $_SESSION['otp_expiry'] < time()) {
        echo json_encode(['success' => false, 'message' => 'OTP expired or not sent. Please request a new one.']);
        exit();
    }

    $stored_otp = $_SESSION['reg_otp'];

    if ($user_otp == $stored_otp) {
        // OTP matched, set flag for successful verification
        $_SESSION['reg_otp_verified'] = true;
        // Clear OTP data from session after successful verification
        unset($_SESSION['reg_otp']);
        unset($_SESSION['otp_expiry']);
        // Crucially, store the email that was verified so the main registration form can check it
        // This is already done when sending OTP: $_SESSION['reg_otp_email'] = $email;
        // We ensure it's cleared upon successful registration.
        echo json_encode(['success' => true, 'message' => 'OTP verified successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP. Please try again.']);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
?>