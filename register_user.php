<?php
// IMPORTANT: Place this at the very top of your PHP file.
// It helps in debugging by displaying errors directly in the browser.
// REMOVE these lines in a production environment!
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Start session to access OTP verification status
header('Content-Type: application/json'); // Set header to indicate JSON response

// Establish database connection
$con = mysqli_connect("localhost", "root", "", "crime_portal");

// Check if connection was successful
if (!$con) {
    // Log the error for server-side debugging
    error_log("Database Connection Failed: " . mysqli_connect_error());
    // Return a JSON error response to the client
    echo json_encode(['success' => false, 'message' => 'Database connection failed. Please try again later.']);
    exit(); // Terminate script execution
}

// Check if the request method is POST and all expected fields are set
if ($_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['address'], $_POST['aadhar_number'], $_POST['gender'], $_POST['mobile_number'])) {

    // Retrieve and trim input data
    $u_name = trim($_POST['name']);
    $u_id = trim($_POST['email']); // Email is used as user ID
    $u_pass_raw = trim($_POST['password']);
    $u_addr = trim($_POST['address']);
    $a_no = trim($_POST['aadhar_number']);
    $gen = trim($_POST['gender']);
    $mob = trim($_POST['mobile_number']);

    // --- Server-side Validation (Crucial for security and data integrity) ---
    // Validate email format
    if (!filter_var($u_id, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit();
    }
    // Validate password length
    if (strlen($u_pass_raw) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long.']);
        exit();
    }
    // Validate Aadhar number format
    if (!preg_match('/^[1-9][0-9]{11}$/', $a_no)) {
        echo json_encode(['success' => false, 'message' => 'Invalid Aadhar number. Aadhar should be 12 digits, not starting with 0.']);
        exit();
    }
    // Validate mobile number format
    if (!preg_match('/^[6-9][0-9]{9}$/', $mob)) {
        echo json_encode(['success' => false, 'message' => 'Invalid mobile number. Must be 10 digits and start with 6, 7, 8, or 9.']);
        exit();
    }
    // Check for empty required text fields (after trimming)
    if (empty($u_name) || empty($u_addr) || empty($gen)) {
        echo json_encode(['success' => false, 'message' => 'Full Name, Address, and Gender are required fields.']);
        exit();
    }

    // --- Critical Security Check: Verify OTP status from session ---
    // This ensures that only users who have successfully verified their email via OTP
    // in the current session can register. Prevents direct access to this script.
    // Note: The logic for 'email_verified' status tracking now relies entirely on the session.
    // If you need a persistent 'email_verified' status in the DB, you MUST add the column.
    if (!isset($_SESSION['reg_otp_verified']) || $_SESSION['reg_otp_verified'] !== true || $_SESSION['reg_otp_email'] !== $u_id) {
        echo json_encode(['success' => false, 'message' => 'Email not verified or session mismatch. Please verify your email with OTP again.']);
        exit();
    }

    // Hash the password securely using bcrypt
    $u_pass = password_hash($u_pass_raw, PASSWORD_BCRYPT);

    // No longer defining $email_verified and $verification_code as they are not inserted into the DB
    // $email_verified = 1;
    // $verification_code = md5(uniqid(rand(), true));

    // Check if email already exists in the database to prevent duplicate registrations
    $stmt_check_email = $con->prepare("SELECT u_id FROM user WHERE u_id = ?");
    if ($stmt_check_email) {
        $stmt_check_email->bind_param("s", $u_id);
        $stmt_check_email->execute();
        $stmt_check_email->store_result(); // Store results to check num_rows

        if ($stmt_check_email->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'This email is already registered. Please use a different email or log in.']);
            $stmt_check_email->close();
            exit();
        }
        $stmt_check_email->close(); // Close the prepared statement for email check
    } else {
        // Log database preparation errors
        error_log("Database Prepare Error (check email): " . $con->error);
        echo json_encode(['success' => false, 'message' => 'A database error occurred during email verification.']);
        exit();
    }

    // Prepare and execute the INSERT statement to add the new user
    // IMPORTANT: 'email_verified' and 'verification_code' columns are REMOVED from this query
    $stmt = $con->prepare("INSERT INTO user (u_name, u_id, u_pass, u_addr, a_no, gen, mob) VALUES (?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        // The 'sssssss' corresponds to 7 string parameters.
        // Parameters for 'email_verified' and 'verification_code' are REMOVED here.
        $stmt->bind_param("sssssss", $u_name, $u_id, $u_pass, $u_addr, $a_no, $gen, $mob);

        if ($stmt->execute()) {
            // Registration successful: Clear OTP-related session variables
            // This is important to prevent re-using the same OTP verification.
            unset($_SESSION['reg_otp']);
            unset($_SESSION['reg_otp_verified']);
            unset($_SESSION['reg_otp_email']);
            unset($_SESSION['otp_expiry']);

            echo json_encode(['success' => true, 'message' => 'Registration successful! You can now log in.']);
        } else {
            // Log execution errors
            error_log("Database Execute Error: " . $stmt->error);
            echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $stmt->error]);
        }
        $stmt->close(); // Close the prepared statement
    } else {
        // Log database preparation errors
        error_log("Database Prepare Error (insert user): " . $con->error);
        echo json_encode(['success' => false, 'message' => 'A database error occurred during registration setup.']);
    }
} else {
    // Respond to invalid request methods or missing parameters
    echo json_encode(['success' => false, 'message' => 'Invalid request method or missing registration data.']);
}

mysqli_close($con); // Close the database connection
?>