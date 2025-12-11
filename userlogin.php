<!DOCTYPE html>
<html>
<head>
<?php
session_start(); // Always start the session at the very beginning of your PHP script

// Initialize message variable
$message = "";

// Check if the login form was submitted
if (isset($_POST['s'])) {

    // Establish database connection
    $conn = mysqli_connect("localhost", "root", "", "crime_portal");

    // Check for connection errors
    if (!$conn) {
        // Log the error to the server's error log (e.g., Apache error log)
        error_log("Login DB Connection failed: " . mysqli_connect_error());
        // Display a generic error message to the user for security
        $message = "An internal error occurred. Please try again later.";
        echo "<script type='text/javascript'>alert('$message');</script>";
    } else {
        // Ensure the request method is POST
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get user input from the form
            // Trim whitespace from beginning/end of inputs
            $u_id = trim($_POST['email']); // User ID (email) entered by the user
            $entered_password = $_POST['password']; // Plain-text password entered by the user

            // Validate inputs (basic server-side check for empty fields)
            if (empty($u_id) || empty($entered_password)) {
                $message = "Please enter both Email ID and Password.";
                echo "<script type='text/javascript'>alert('$message');</script>";
            } else {
                // --- Step 1: Prepare the SQL query to prevent SQL injection ---
                $query = "SELECT u_id, u_pass FROM user WHERE u_id = ?";
                $stmt = mysqli_prepare($conn, $query);

                if ($stmt) {
                    // --- Step 2: Bind parameters and execute the query ---
                    mysqli_stmt_bind_param($stmt, "s", $u_id); // 's' indicates a string parameter for u_id
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    // --- Step 3: Check if a user with the given ID was found ---
                    if (mysqli_num_rows($result) > 0) {
                        $row = mysqli_fetch_assoc($result);
                        $stored_hashed_password = $row['u_pass']; // This is the hashed password from the database

                        // --- Step 4: Verify the entered password against the stored hash ---
                        // Use password_verify() for secure password comparison
                        if (password_verify($entered_password, $stored_hashed_password)) {
                            // Password matches! Login successful.
                            $_SESSION['x'] = 1; // Your custom session variable
                            $_SESSION['u_id'] = $row['u_id']; // Store the user ID in the session

                            // Redirect to the complainer page
                            header("Location: complainer_page.php");
                            exit(); // Always exit after a header redirect to prevent further script execution
                        } else {
                            // Password does NOT match
                            $message = "Invalid Email ID or Password."; // Generic message for security
                            echo "<script type='text/javascript'>alert('$message');</script>";
                        }
                    }
                    mysqli_stmt_close($stmt); // Close the prepared statement
                } else {
                    // Error preparing the statement
                    $message = "An internal error occurred. Please try again.";
                    error_log("Failed to prepare login statement: " . mysqli_error($conn));
                    echo "<script type='text/javascript'>alert('$message');</script>";
                }
            }
        }
    }
    // Close the database connection if it was successfully opened
    if ($conn) {
        mysqli_close($conn);
    }
}
?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

    <script>
      // Client-side validation to prevent spaces in inputs
      function f1() {
            var emailInput = document.getElementById("exampleInputEmail1");
            var passwordInput = document.getElementById("exampleInputPassword1");

            var emailValue = emailInput.value;
            var passwordValue = passwordInput.value;

            // Check for spaces in email and password
            if (emailValue.includes(' ')) {
                alert("Space Not Allowed in Email ID");
                emailInput.value = ""; // Clear the input
                emailInput.focus(); // Set focus back to the input
                return false; // Prevent form submission
            }
            if (passwordValue.includes(' ')) {
                alert("Space Not Allowed in Password");
                passwordInput.value = ""; // Clear the input
                passwordInput.focus(); // Set focus back to the input
                return false; // Prevent form submission
            }
            return true; // Allow form submission if no issues
      }
    </script>

<style>
  body {
    background-size: cover;
    background-image: url(regi_bg.jpeg); /* Ensure this path is correct */
    background-position: center;
    background-repeat: no-repeat;
    min-height: 100vh; /* Ensure body takes full viewport height */
    display: flex;
    flex-direction: column; /* For footer positioning */
  }

  .navbar {
    height: 60px;
  }

  .navbar-brand {
    margin-top: 5%; /* Adjust as needed */
  }

  .form {
    margin-top: 15%; /* Adjust as needed */
    flex-grow: 1; /* Allows the form div to take available space */
  }

  .form-group {
    width: 30%; /* Adjust as needed */
    margin: 0 auto 15px auto; /* Center and add bottom margin */
  }

  .button-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 30%; /* Match width of form-group */
      margin: 10px auto; /* Center the button container */
  }

  .btn-primary {
      border-radius: 8px;
  }

  .forgot-password-link {
      color: #ffffff;
      text-decoration: none;
  }

  .footer {
    position: relative; /* Changed from fixed to relative for better flow with flexbox */
    bottom: 0;
    width: 100%;
    background-color: rgba(0,0,0,0.7);
    color: white;
    text-align: center;
    padding: 10px 0; /* Add some padding */
    margin-top: auto; /* Pushes the footer to the bottom */
  }
</style>

<title>Complainant Login</title>
</head>
<body>
  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <a class="navbar-brand" href="home.php"><b>Crime Portal</b></a>
      </div>
      <div id="navbar" class="collapse navbar-collapse">
        <ul class="nav navbar-nav">
          <li class="active"><a href="userlogin.php">Complainer Login</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div align="center">
    <div class="form">
      <form method="post" onsubmit="return f1()"> <div class="form-group">
          <label for="exampleInputEmail1"><h1 style="color: #fff;">User Login</h1></label>
          <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Email id" required name="email">
        </div>
        <div class="form-group">
          <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password" required name="password">
        </div>

        <div class="button-container">
          <button type="button" class="btn btn-primary"><a href="forgot_password.php" class="forgot-password-link">Forgot Password?</a></button>
          <button type="submit" class="btn btn-primary" name="s">Submit</button>
        </div>
      </form>
    </div>
  </div>

  <div class="footer">
    <h4 style="color: white;">&copy; <b>Crime Portal 2025</b></h4>
  </div>

</body>
</html>