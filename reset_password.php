<?php
session_start();
$message = "";
$conn = null; // Initialize connection variable

// Ensure a token is present in the URL
if (!isset($_GET['token']) || empty($_GET['token'])) {
    header("Location: forgot_password.php");
    exit();
}

$token = $_GET['token'];

// Establish database connection
// IMPORTANT: Replace with your actual database credentials
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "crime_portal";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    // In a production environment, log this error instead of displaying it.
    // die("Connection failed: " . mysqli_connect_error());
    error_log("Database Connection failed: " . mysqli_connect_error());
    $message = "An internal server error occurred. Please try again later.";
} else {
    // --- Step 1: Validate Token using Prepared Statement ---
    $query = "SELECT email, expiry FROM password_reset WHERE token = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $email = $row['email'];
            $expiry = $row['expiry'];

            if (strtotime($expiry) < time()) {
                $message = "The reset link has expired.";
                // Optionally, delete the expired token here to clean up
                $delete_expired_token_sql = "DELETE FROM password_reset WHERE token = ?";
                $stmt_delete_expired = mysqli_prepare($conn, $delete_expired_token_sql);
                if ($stmt_delete_expired) {
                    mysqli_stmt_bind_param($stmt_delete_expired, "s", $token);
                    mysqli_stmt_execute($stmt_delete_expired);
                    mysqli_stmt_close($stmt_delete_expired);
                }
            } elseif (isset($_POST['reset'])) {
                // --- Step 2: Server-Side Password Confirmation ---
                $new_password = $_POST['password'];
                $con_password = $_POST['con_password'];

                if (empty($new_password) || empty($con_password)) {
                    $message = "Both password fields are required.";
                } elseif ($new_password !== $con_password) {
                    $message = "New password and confirm password do not match.";
                }
                // --- Step 3: Password Strength Validation (Recommended) ---
                elseif (strlen($new_password) < 6) {
                    $message = "Password must be at least 6 characters long.";
                }
                // You can add more complex password requirements (e.g., must contain a number, a symbol, etc.)
                elseif (!preg_match('/[A-Z]/', $new_password) || !preg_match('/[a-z]/', $new_password) || !preg_match('/\d/', $new_password)) {
                    $message = "Password must contain at least one uppercase letter, one lowercase letter, and one number.";
                }
                else {
                    // --- Step 4: Secure Password Hashing ---
                    // Use PASSWORD_BCRYPT for strong, modern hashing.
                    $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);

                    // --- Step 5: Update Password in the User Table using Prepared Statement ---
                    $update_sql = "UPDATE user SET u_pass = ? WHERE u_id = ?"; // Assuming u_id is the user's identifier
                    $stmt_update = mysqli_prepare($conn, $update_sql);

                    if ($stmt_update) {
                        mysqli_stmt_bind_param($stmt_update, "ss", $new_password_hashed, $email); // 'ss' for two string parameters
                        if (mysqli_stmt_execute($stmt_update)) {
                            $message = "Password has been reset successfully. You will be redirected to the login page.";

                            // --- Step 6: Delete the Token after Successful Reset using Prepared Statement ---
                            $delete_token_sql = "DELETE FROM password_reset WHERE token = ?";
                            $stmt_delete = mysqli_prepare($conn, $delete_token_sql);
                            if ($stmt_delete) {
                                mysqli_stmt_bind_param($stmt_delete, "s", $token);
                                mysqli_stmt_execute($stmt_delete);
                                mysqli_stmt_close($stmt_delete);
                            }

                            // Redirect to the login page after a short delay
                            header("Refresh: 3; URL=userlogin.php");
                            exit();
                        } else {
                            $message = "Failed to reset password. Please try again. (Database Update Error)";
                            error_log("Password update failed: " . mysqli_error($conn));
                        }
                        mysqli_stmt_close($stmt_update);
                    } else {
                        $message = "An internal error occurred during password update. Please try again.";
                        error_log("Failed to prepare update statement: " . mysqli_error($conn));
                    }
                }
            }
        } else {
            $message = "Invalid or expired token.";
        }
        mysqli_stmt_close($stmt); // Close the statement for token validation
    } else {
        $message = "An internal error occurred. Please try again.";
        error_log("Failed to prepare select statement: " . mysqli_error($conn));
    }
}

// Close the database connection if it was opened
if ($conn) {
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Client-side validation for better user experience
        function validatePasswordForm() {
            const password = document.getElementById('password').value;
            const con_password = document.getElementById('con_password').value;
            let isValid = true;
            let errorMessage = '';

            if (password === '' || con_password === '') {
                errorMessage = 'Both password fields are required.';
                isValid = false;
            } else if (password !== con_password) {
                errorMessage = 'New password and confirm password do not match.';
                isValid = false;
            } else if (password.length < 8) {
                errorMessage = 'Password must be at least 8 characters long.';
                isValid = false;
            }
            //Add more client-side password complexity checks here to match server-side
            else if (!/[A-Z]/.test(password) || !/[a-z]/.test(password) || !/\d/.test(password)) {
                errorMessage = "Password must contain at least one uppercase letter, one lowercase letter, and one number.";
                isValid = false;
            }

            const alertDiv = document.querySelector('.alert.alert-info');
            if (isValid) {
                if (alertDiv) alertDiv.style.display = 'none'; // Hide existing messages
                return true; // Allow form submission
            } else {
                if (alertDiv) {
                    alertDiv.textContent = errorMessage;
                    alertDiv.style.display = 'block';
                } else {
                    // Create an alert div if it doesn't exist (useful for initial load or if no alert present)
                    const container = document.querySelector('.container');
                    const newAlert = document.createElement('div');
                    newAlert.className = 'alert alert-info text-center';
                    newAlert.textContent = errorMessage;
                    const formElement = container.querySelector('form');
                    if (formElement) {
                        container.insertBefore(newAlert, formElement);
                    } else {
                         // Fallback if form is not found (e.g., if token expired and form isn't shown)
                        container.appendChild(newAlert);
                    }
                }
                return false; // Prevent form submission
            }
        }

    if (!isset($_GET['token']) || empty($_GET['token'])) {
    header("Location: forgot_password.php");
    exit();
}
    
    </script>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Reset Password</h2>
    <?php if ($message != ""): ?>
        <div class="alert alert-info text-center"> <?= htmlspecialchars($message) ?> </div>
    <?php endif; ?>
    <?php
    // Only display the form if the token is valid and not expired,
    // and a password reset hasn't just completed successfully.
    // The conditions check for messages that imply the form should still be shown for user input.
    $displayForm = false;
    if (empty($message) ||
        strpos($message, 'expired') !== false ||
        strpos($message, 'Invalid') !== false ||
        strpos($message, 'match') !== false ||
        strpos($message, 'required') !== false ||
        strpos($message, 'length') !== false ||
        strpos($message, 'Failed to reset') !== false ||
        strpos($message, 'internal error') !== false) {
        $displayForm = true;
    }

    if ($displayForm && mysqli_num_rows($result ?? false) > 0 && strtotime($expiry ?? '') >= time()):
        ?>
        <form method="post" onsubmit="return validatePasswordForm()">
            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="Enter new password">
            </div>
            <div class="mb-3">
                <label for="con_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="con_password" name="con_password" required placeholder="Re-enter new password">
            </div>
            <button type="submit" name="reset" class="btn btn-primary">Reset Password</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>