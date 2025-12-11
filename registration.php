<!DOCTYPE html>
<html>
<?php
session_start();
?>

<head>
  <title>User Registration</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  <link href="http://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet">
  <link href="complainer_page.css" rel="stylesheet" type="text/css" />
  <meta name="viewport" content="width=device-width, initial-scale=0.9">

<style>
  body {
      background-color: #2c3e50;
      font-family: 'Lato', sans-serif;
  }

  .login-form {
      max-width: 380px;
      width: 100%;
      background: #34495e;
      padding: 30px;
      border-radius: 10px;
      margin: 5% auto;
      color: white;
      position: auto;
  }

  input[type="text"], input[type="email"], input[type="password"], select {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      background: #4e4e4e;
      color: #dfdfdf;
      border: none;
      border-radius: 5px;
  }

  /* Base style for buttons to ensure consistency */
  input[type="submit"], button[type="button"] {
      background: #d670ff;
      color: #dfdfdf;
      padding: 6px 12px;
      font-size: 14px;
      border: none;
      border-radius: 6px;
  }

  /* Specific style for Send OTP and Verify OTP buttons */
  #sendOtpBtn {
      min-width: 80px;
      min-height: 36px;
      background-color: #d670ff;
      color: #ffffff;
      margin-top: -11px; 
      box-shadow: 0 2px 6px #000000;
      font-size: 0.9em;
      letter-spacing: 0.5px;
  }

#verifyOtpBtn {
    min-width: 80px;
    min-height: 36px;
    background-color: #d670ff;
    color: #ffffff;
    margin-top: -11px; 
    box-shadow: 0 2px 6px #000000;
    font-size: 0.8em;
    text-align: center;
    letter-spacing: 0.5px;
    /* Make button width flexible
    width: auto;*/
    max-width: 100%;
    /*box-sizing: border-box; */
}

@media (max-width: 480px) {
    #verifyOtpBtn {
        min-width: 60px;
        font-size: 1em;
        padding: 8px 10px;
        margin-top: 0;
    }
}

  /* Style for the main Submit button, adjusted for centering and size */
  #submitBtn {
    background-color: #d670ff;
    color: #ffffff;
    font-size: 1.1em;
    border: none;
    border-radius: 6px;
    box-shadow: 0 8px 10px #000000;
    font-weight: 700;
    letter-spacing: 1px;
    width: 120px;
    padding: 8px 15px;
    display: inline-block;
    margin-top: 15px;
    margin-left: 14px;
}

  /* Styles for verified state */
  .email-verified-success {
    color: #28a745; /* Green color */
    font-weight: bold;
    width: 100%;
    display: inline-block;
    text-align: center; /* Center the text */
    margin-bottom: 15px;
}

  /* Custom Spinner Styles */
  /* Attractive Custom Spinner Styles */
.loader {
  width: 1.5em;
  height: 1.5em;
  border-radius: 50%;
  position: relative;
  animation: rotate 1s linear infinite;
  display: none; /* Hidden by default */
  margin-left: 10px;
  vertical-align: middle;
}

.loader::before {
  content: '';
  box-sizing: border-box;
  position: absolute;
  inset: 0;
  border-radius: 50%;
  border: 3px solid transparent;
  border-top-color: #3498db;
  border-left-color: #8ecdf7;
  animation: spinGlow 1s linear infinite;
  box-shadow: 0 0 8px #3498db;
}

/* Rotation animation */
@keyframes rotate {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Glowing effect animation */
@keyframes spinGlow {
  0% { box-shadow: 0 0 8px #3498db; }
  50% { box-shadow: 0 0 16px #8ecdf7; }
  100% { box-shadow: 0 0 8px #3498db; }
}


  /* Toast Notification Styles */
  #toastNotification {
    visibility: hidden; /* Hidden by default. */
    min-width: 250px;
    background-color: #333;
    color: #fff;
    text-align: center;
    border-radius: 5px;
    padding: 16px;
    position: fixed;
    z-index: 1000; /* Sit on top of everything */
    left: 50%; /* Center the toast */
    transform: translateX(-50%);
    top: 60px; /* Position it at the top */
    font-size: 17px;
    white-space: nowrap; /* Prevent text wrapping */
    opacity: 0;
    transition: visibility 0s, opacity 0.5s linear; /* Smooth fade effect */
  }

  #toastNotification.show {
    visibility: visible; /* Show the toast */
    opacity: 1; /* Fade in */
  }

  #toastNotification.success {
    background-color: #4CAF50; /* Green for success */
  }

  #toastNotification.error {
    background-color: #f44336; /* Red for error */
  }

  #toastNotification.info {
    background-color: #2196F3; /* Blue for info */
  }
</style>
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="home.php"><b>Crime Portal</b></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="#">Registration</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="video" style="margin-top: -1%;">
    <div class="center-container">
        <div class="bg-agile">
            <br><br>
            <div class="login-form" style="margin-bottom: -1%;">
                <form id="registrationForm" action="#" method="post">
                    <p style="color:#dfdfdf">Full Name</p>
                    <input type="text" name="name" required id="name1" />

                    <p style="color:#dfdfdf">Email-Id</p>
                    <div style="display: flex; gap: 8px; align-items: center; width: 100%;">
                        <input type="email" name="email" required id="email1" style="flex:1;" />
                        <button type="button" class="btn btn-info" id="sendOtpBtn">Send OTP</button>
                        <div class="loader" id="sendOtpLoader"></div>
                    </div>
                    <span id="emailVerifiedStatus" class="email-verified-success" style="display:none; margin-bottom: 15px; "></span>

                    <div id="otpSection" style="display:none; margin-bottom: 15px;">
                        <label for="reg_otp" style="color:#dfdfdf">Enter OTP</label>
                        <div style="display: flex; gap: 8px; align-items: center; width: 100%;">
                            <input type="text" id="reg_otp" name="reg_otp" maxlength="6" class="form-control" autocomplete="off" style="margin-bottom: 15px; border-radius: 5px; height: 39px; width: 100%;"/>
                            <button type="button" class="btn btn-primary" id="verifyOtpBtn">Verify OTP</button>
                            <div class="loader" id="verifyOtpLoader"></div>
                        </div>
                        <span id="otpStatus" style="margin-left:10px; font-weight:600;"></span>
                    </div>

                    <p style="color:#dfdfdf; margin-bottom: 10px;">Password</p>
                    <input type="password" name="password" placeholder="6 Character minimum" pattern=".{6,}" required id="pass" style="margin-bottom: 15px; border-radius: 5px; width: 100%;"/>

                    <p style="color:#dfdfdf">Home Address</p>
                    <input type="text" name="address" required id="addr" />

                    <p style="color:#dfdfdf">Aadhar Number</p>
                    <input type="text" name="aadhar_number" minlength="12" maxlength="12" pattern="[1-9][0-9]{11}" required id="aadh" />

                    <div class="left-w3-agile">
                        <p style="color:#dfdfdf">Gender</p>
                        <select class="form-control" name="gender" id="gender">
                            <option>Male</option>
                            <option>Female</option>
                            <option>Others</option>
                        </select>
                    </div>

                    <div class="right-agileits">
                        <p style="color:#dfdfdf">Mobile</p>
                        <input type="text" name="mobile_number" required pattern="[6789][0-9]{9}" minlength="10" maxlength="10" id="mobno" />
                    </div>

                    <div style="text-align: center;">
                        <button type="button" id="submitBtn" class="btn btn-success">Submit</button>
                        <div class="loader" id="submitLoader" style="margin-top: 20px;"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="toastNotification"></div>

<script>
    document.getElementById('submitBtn').addEventListener('click', function () {
  const spinner = document.getElementById('spinner');
  spinner.style.display = 'inline-block';

  setTimeout(() => {
    spinner.style.display = 'none';
  }, 3000); 
});

    let otpVerified = false; // Flag to track if OTP has been successfully verified

    // Get references to loader elements
    const sendOtpLoader = document.getElementById('sendOtpLoader');
    const verifyOtpLoader = document.getElementById('verifyOtpLoader');
    const submitLoader = document.getElementById('submitLoader'); // New loader for submit button
    const toastNotification = document.getElementById('toastNotification');
    const submitBtn = document.getElementById('submitBtn'); // Reference to the submit button
    const emailInput = document.getElementById('email1'); // Reference to the email input
    const sendOtpBtn = document.getElementById('sendOtpBtn'); // Reference to the Send OTP button
    const otpSection = document.getElementById('otpSection'); // Reference to the OTP section
    const emailVerifiedStatus = document.getElementById('emailVerifiedStatus'); // Reference to verified status text

    // --- Custom Spinner Functions ---
    function showCustomSpinner(loaderElement) {
        if (loaderElement) {
            loaderElement.style.display = 'inline-block';
        }
    }

    function hideCustomSpinner(loaderElement) {
        if (loaderElement) {
            loaderElement.style.display = 'none';
        }
    }

    // --- Toast Notification Function ---
    function showToast(message, type = 'info', duration = 5000) {
        toastNotification.textContent = message;
        toastNotification.className = 'show ' + type; // Add 'show' class and type for styling

        setTimeout(function(){
            toastNotification.className = toastNotification.className.replace('show', '');
        }, duration);
    }

    // --- Function to reset email and OTP related fields ---
    function resetEmailAndOtpFields() {
        otpVerified = false;
        emailInput.readOnly = false; // Make email field editable
        emailInput.disabled = false; // Ensure it's not disabled
        sendOtpBtn.disabled = false; // Re-enable Send OTP button
        sendOtpBtn.style.display = 'inline-block'; // Show the Send OTP button
        otpSection.style.display = 'none'; // Hide OTP section
        emailVerifiedStatus.style.display = 'none'; // Hide email verified status
        document.getElementById('reg_otp').value = ''; // Clear OTP input
        document.getElementById('reg_otp').disabled = false; // Re-enable OTP input
        document.getElementById('verifyOtpBtn').disabled = false; // Re-enable Verify OTP button
        document.getElementById('otpStatus').textContent = ''; // Clear OTP status message
    }


    // --- Function to collect and submit all registration data ---
    async function submitRegistrationData() {
        // Essential: Do not proceed if OTP is not verified
        if (!otpVerified) {
            showToast('Email not verified. Please verify your email with OTP first.', 'error');
            return;
        }

        // Perform client-side validation first before sending data
        if (!validateInput()) {
            return; // If validation fails, stop
        }

        // Gather all form data
        const formData = new URLSearchParams();
        formData.append('name', document.getElementById('name1').value.trim());
        formData.append('email', document.getElementById('email1').value.trim());
        formData.append('password', document.getElementById('pass').value.trim());
        formData.append('address', document.getElementById('addr').value.trim());
        formData.append('aadhar_number', document.getElementById('aadh').value.trim());
        formData.append('gender', document.getElementById('gender').value.trim());
        formData.append('mobile_number', document.getElementById('mobno').value.trim());

        // Disable submit button and show loader
        submitBtn.disabled = true;
        showCustomSpinner(submitLoader);

        try {
            const response = await fetch('register_user.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData
            });

            const data = await response.json();

            hideCustomSpinner(submitLoader); // Hide spinner

            if (data.success) {
                showToast(data.message, 'success');
                // Redirect to login page after a short delay to allow toast to be seen
                setTimeout(() => {
                    window.location.href = 'userlogin.php';
                }, 1000); // Redirect after a seconds
            } else {
                showToast(data.message, 'error');
                submitBtn.disabled = false; // Re-enable button on failure

                // --- NEW LOGIC FOR ALREADY REGISTERED EMAIL ---
                if (data.message.includes('already registered')) { // Check for specific message from server
                    resetEmailAndOtpFields(); // Re-enable email field and OTP process
                    emailInput.focus(); // Focus on the email field
                }
                // --- END NEW LOGIC ---
            }
        } catch (error) {
            console.error('Error during registration:', error);
            hideCustomSpinner(submitLoader);
            submitBtn.disabled = false; // Re-enable button on error
            showToast('An unexpected error occurred during registration. Please try again.', 'error');
            resetEmailAndOtpFields(); // Consider resetting on unexpected error too
        }
    }

    // Event handler for the "Send OTP" button click
    document.getElementById('sendOtpBtn').onclick = function() {
        const email = document.getElementById('email1').value.trim();

        if (!email) {
            showToast('Please enter your email first.', 'error');
            return;
        }

        // Prevent sending OTP again if already verified
        if (otpVerified) {
            showToast('Email is already verified. You can proceed with registration.', 'info');
            return;
        }

        document.getElementById('sendOtpBtn').disabled = true;
        document.getElementById('email1').disabled = true; // Disable email field to prevent modification
        showCustomSpinner(sendOtpLoader);

        // Hide any previous verification statuses or OTP sections
        document.getElementById('emailVerifiedStatus').style.display = 'none';
        document.getElementById('otpStatus').textContent = '';
        document.getElementById('otpSection').style.display = 'none';
        document.getElementById('reg_otp').value = ''; // Clear any pre-filled OTP
        document.getElementById('reg_otp').disabled = false; // Ensure OTP input is enabled for new attempt
        document.getElementById('verifyOtpBtn').disabled = false; // Ensure verify button is enabled for new attempt


        fetch('send_reg_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'email=' + encodeURIComponent(email)
        })
        .then(r => {
            if (!r.ok) {
                throw new Error(`HTTP error! status: ${r.status}`);
            }
            return r.json();
        })
        .then(data => {
            document.getElementById('sendOtpBtn').disabled = false;
            document.getElementById('email1').disabled = false;
            hideCustomSpinner(sendOtpLoader);

            showToast(data.message, data.success ? 'success' : 'error');

            if (data.success) {
                document.getElementById('otpSection').style.display = 'block';
                document.getElementById('reg_otp').focus();
            }
        })
        .catch(error => {
            console.error('Error sending OTP:', error);
            document.getElementById('sendOtpBtn').disabled = false;
            document.getElementById('email1').disabled = false;
            hideCustomSpinner(sendOtpLoader);
            showToast('Failed to send OTP. Please try again. (Check console for details)', 'error');
        });
    };

    // Event handler for the "Verify OTP" button click
    document.getElementById('verifyOtpBtn').onclick = function() {
        const otp = document.getElementById('reg_otp').value.trim();

        if (!otp) {
            showToast('Please enter the OTP.', 'error');
            return;
        }

        document.getElementById('verifyOtpBtn').disabled = true;
        document.getElementById('reg_otp').disabled = true;
        showCustomSpinner(verifyOtpLoader);

        fetch('verify_reg_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'otp=' + encodeURIComponent(otp)
        })
        .then(r => r.json())
        .then(data => {
            hideCustomSpinner(verifyOtpLoader);

            document.getElementById('otpStatus').textContent = data.message;

            if (data.success) {
                otpVerified = true;
                document.getElementById('otpStatus').style.color = 'green';

                // Disable OTP field and button as verification is complete
                document.getElementById('reg_otp').disabled = true;
                document.getElementById('verifyOtpBtn').disabled = true;

                // Make email field read-only and disable Send OTP button permanently
                document.getElementById('email1').readOnly = true;
                document.getElementById('sendOtpBtn').disabled = true;
                document.getElementById('sendOtpBtn').style.display = 'none'; // Hide the button

                // Hide the entire OTP input section
                document.getElementById('otpSection').style.display = 'none';

                // Display the "Email Verified!" success message and checkmark
                const emailVerifiedStatus = document.getElementById('emailVerifiedStatus');
                emailVerifiedStatus.textContent = 'Email Verified! ✔';
                emailVerifiedStatus.style.display = 'inline-block';
                emailVerifiedStatus.style.color = '#28a745';
                showToast(data.message, 'success');

                // --- Trigger registration data submission after successful OTP verification ---
                // This is the key change to automatically register after email verification.
                submitRegistrationData();

            } else {
                otpVerified = false; // Ensure flag is false if verification fails
                document.getElementById('otpStatus').style.color = 'red';
                // Re-enable OTP field and button so user can try again
                document.getElementById('reg_otp').disabled = false;
                document.getElementById('verifyOtpBtn').disabled = false;
                // Hide the success message if verification fails
                document.getElementById('emailVerifiedStatus').style.display = 'none';
                showToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error verifying OTP:', error);
            hideCustomSpinner(verifyOtpLoader);
            document.getElementById('reg_otp').disabled = false;
            document.getElementById('verifyOtpBtn').disabled = false;

            document.getElementById('otpStatus').textContent = 'Error verifying OTP. Please try again.';
            document.getElementById('otpStatus').style.color = 'red';
            document.getElementById('emailVerifiedStatus').style.display = 'none';
            showToast('Error verifying OTP. Please try again.', 'error');
        });
    };

    // Attaching submitRegistrationData to the "Submit Registration" button's click event.
    // While the primary flow is automatic after OTP, this provides a manual trigger if needed.
    submitBtn.onclick = submitRegistrationData;


    // Client-side form validation before sending data to register_user.php
    function validateInput() {
        const fields = [
            { id: "name1", noSpaces: false, trimCheck: true, name: "Full Name" },
            { id: "email1", noSpaces: false, trimCheck: true, name: "Email-Id" },
            { id: "pass", noSpaces: true, name: "Password" },
            { id: "addr", trimCheck: true, name: "Home Address" },
            { id: "aadh", noSpaces: true, name: "Aadhar Number" },
            { id: "mobno", noSpaces: true, name: "Mobile Number" }
        ];

        for (const field of fields) {
            const el = document.getElementById(field.id);
            if (!el) {
                console.warn(`Element with ID "${field.id}" not found.`);
                continue;
            }
            const value = el.value;
            const fieldDisplayName = field.name || el.id;

            if (field.trimCheck && value.trim() === "") {
                showToast(`The "${fieldDisplayName}" field cannot be empty or just spaces.`, 'error');
                el.value = "";
                el.focus();
                return false;
            }

            // Check for spaces in fields that should not contain any spaces (e.g., password, Aadhar, mobile)
            if (field.noSpaces && value.indexOf(" ") >= 0) {
                showToast(`Spaces are not allowed in the "${fieldDisplayName}" field.`, 'error');
                el.value = "";
                el.focus();
                return false;
            }
        }

        // Additional specific validations (regex, length etc.)
        const email = document.getElementById('email1').value;
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showToast('Please enter a valid email address.', 'error');
            document.getElementById('email1').focus();
            return false;
        }

        const password = document.getElementById('pass').value;
        if (password.length < 6) {
            showToast('Password must be at least 6 characters long.', 'error');
            document.getElementById('pass').focus();
            return false;
        }

        const aadhar = document.getElementById('aadh').value;
        if (!/^[1-9][0-9]{11}$/.test(aadhar)) {
            showToast('Please enter a valid 12-digit Aadhar number (digits 0-9, first digit 1-9).', 'error');
            document.getElementById('aadh').focus();
            return false;
        }

        const mobile = document.getElementById('mobno').value;
        if (!/^[6-9][0-9]{9}$/.test(mobile)) {
            showToast('Please enter a valid 10-digit Indian mobile number (starts with 6, 7, 8, or 9).', 'error');
            document.getElementById('mobno').focus();
            return false;
        }

        return true;
    }
</script>

<script src="https://code.jquery.com/jquery-2.1.4.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>