<!DOCTYPE html>
<html>
 
<?php
session_start();

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

if (!isset($_SESSION['x'])) {
    header("Location: userlogin.php");
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "crime_portal");
if (!$conn) {
    die("Could not connect: " . mysqli_error($conn));
}

// Get user info
$u_id = $_SESSION['u_id'];
$stmt = mysqli_prepare($conn, "SELECT a_no, u_name FROM user WHERE u_id=?");
mysqli_stmt_bind_param($stmt, "s", $u_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
$u_name = $user['u_name'];
$a_no = $user['a_no'];


if (isset($_POST['s']) && $_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $location    = trim(mysqli_real_escape_string($conn, $_POST['location']));
    $type_crime  = trim(mysqli_real_escape_string($conn, $_POST['type_crime']));
    $d_o_c       = $_POST['d_o_c'];
    $description = trim(mysqli_real_escape_string($conn, $_POST['description']));

    // Validate date
    $var = strtotime(date("Ymd")) - strtotime($d_o_c);
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $d_o_c)) {
      echo "<script>showToast('Invalid date format.');</script>";
      exit;
    }

    // Generate c_id: e.g., BANG2025-0001
    $location_code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $location), 0, 4));
    $year = date("Y");
    $prefix = $location_code . $year . '-';

    // Find last used ID with same prefix
    $id_query = mysqli_query($conn, "
        SELECT c_id FROM complaint
        WHERE c_id LIKE '{$prefix}%'
        ORDER BY c_id DESC
        LIMIT 1
    ");
    $serial = 1;
    if ($id_row = mysqli_fetch_assoc($id_query)) {
        $last_cid = $id_row['c_id']; // e.g., BANG2025-0042
        $last_serial = (int)substr($last_cid, -4);
        $serial = $last_serial + 1;
    }

    $next_id = $prefix . str_pad($serial, 4, '0', STR_PAD_LEFT); // BANG2025-0001

    // Fetch incharge email for the selected location
    $email_res = mysqli_query($conn, "SELECT i_name, i_email FROM incharge WHERE location = '$location'");
    if ($email_row = mysqli_fetch_assoc($email_res)) {
        $to = $email_row['i_email'];
        $incharge_name = $email_row['i_name'];
        $subject = "New Complaint Registered";
        $message = "Dear $incharge_name,\n\nA new complaint (ID: $next_id) has been registered for your station ($location).\n\nPlease review it in the Crime Portal.\n\nRegards,\nCrime Portal System";

        $mail = new PHPMailer(true);
        if($var>=0){
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'onlinecrimeportal@gmail.com'; // your Gmail
            $mail->Password = 'wqme eref jtxp wsmf'; // App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('onlinecrimeportal@gmail.com', 'Crime Portal');
            $mail->addAddress($to, $incharge_name);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            if ($mail->send()) {
                // Only insert complaint if email sent
                $stmt = mysqli_prepare($conn, "
                    INSERT INTO complaint
                    (c_id, a_no, location, type_crime, d_o_c, description, inc_status, pol_status, p_id)
                    VALUES (?, ?, ?, ?, ?, ?, 'Unassigned', NULL, NULL)
                ");
                mysqli_stmt_bind_param($stmt, "sissss", $next_id, $a_no, $location, $type_crime, $d_o_c, $description);

                if (mysqli_stmt_execute($stmt)) {
                    echo "<script>alert('Complaint Registered Successfully — ID: {$next_id}, Assigned to: $incharge_name');</script>";

                    // Get user's email to send confirmation
                    $user_email_res = mysqli_query($conn, "SELECT u_id FROM user WHERE u_id='$u_id'");
                    if ($user_email_row = mysqli_fetch_assoc($user_email_res)) {
                        $user_email = $user_email_row['u_id'];
                        $user_subject = "Your Complaint Has Been Registered";
                        $user_message = "Dear $u_name,\n\nYour complaint (ID: $next_id) has been registered successfully for $location regarding $type_crime.\n\nThank you for using the Crime Portal.\n\nRegards,\nCrime Portal";

                        $user_mail = new PHPMailer(true);
                        try {
                            $user_mail->isSMTP();
                            $user_mail->Host = 'smtp.gmail.com';
                            $user_mail->SMTPAuth = true;
                            $user_mail->Username = 'onlinecrimeportal@gmail.com';
                            $user_mail->Password = 'wqme eref jtxp wsmf';
                            $user_mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $user_mail->Port = 587;

                            $user_mail->setFrom('onlinecrimeportal@gmail.com', 'Crime Portal');
                            $user_mail->addAddress($user_email, $u_name);
                            $user_mail->Subject = $user_subject;
                            $user_mail->Body    = $user_message;
                            $user_mail->send();
                            
                            echo "<script>alert('A confirmation email has been sent to your registered email address.');</script>";
                        } catch (Exception $e) {
                            error_log('User mail error: ' . $user_mail->ErrorInfo);
                            echo "<script>alert('Error sending confirmation email.');</script>";
                        }
                    }
                }  else {
                      echo "<script>alert('Database Insert Failed: " . mysqli_stmt_error($stmt) . "');</script>";
                  }
                mysqli_stmt_close($stmt);
            } else {
                echo "<script>alert('Complaint not registered because email to incharge failed.');</script>";
            }
            
        } catch (Exception $e) {
            error_log('PHPMailer error: ' . $mail->ErrorInfo);
            echo "<script>alert('Mailer Error: {$mail->ErrorInfo}');</script>";
        }
        }else {
              echo "<script>alert('Please select a valid date of the crime.');</script>";
            }
    } else {
        echo "<script>alert('No police station found for the selected location.');</script>";
    }
}

?>
    
<script>
  function f1()
  {
    var sta1=document.getElementById("desc").value;
    var x1=sta1.trim();
    if(sta1!="" && x1==""){
      document.getElementById("desc").value="";
      document.getElementById("desc").focus();
      alert("Space Found");
    }
  }

  document.querySelector("form").addEventListener("submit", function() {
    this.querySelector('input[type="submit"]').disabled = true;
  });

 </script>
   
<head>
	<title>Complainer Home Page</title>

	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">
	<link href="complainer_page.css" rel="stylesheet" type="text/css" media="all" />
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

</head>

<body style="background-size: cover;
    background-image: url(home_bg1.jpeg);
    background-position: center;">
	<nav  class="navbar navbar-default navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="#"><b>Crime Portal</b></a>
    </div>
    <div id="navbar" class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <!-- <li ><a href="userlogin.php">User Login</a></li> -->
        <li class="active"><a href="complainer_page.php">User Home</a></li>
      </ul>
     
      <ul class="nav navbar-nav navbar-right">
        <li class="active"><a href="complainer_page.php">Log New Complaint</a></li>
        <li><a href="complainer_complain_history.php">Complaint History</a></li>
        <li><a href="#" id="profileBtn">Profile &nbsp;<i class="fa fa-user" aria-hidden="true"></i></a></li>
      </ul>
    </div>
  </div>
 </nav>
    
    
<div class="video" style="margin-top: 5%"> 
	<div class="center-container">
		 <div class="bg-agile">
			<br><br>
			<div class="login-form"><p><h2 style="color:white">Welcome <?php echo htmlspecialchars($u_name) ?></h2></p><br>
                                    <p><h2>Log New Complain</h2></p><br>	
				<form action="#" method="post" style="color: #dfdfdf ;margin-left: 33px ;">Aadhar
				<br>	<input type="text"  name="aadhar_number" placeholder="Aadhar Number" required="" disabled value=<?php echo "$a_no"; ?> style="width:312px ;">
					
				<div class="top-w3-agile" style="color: #dfdfdf">Location of Crime
                    
                    <select class="form-control" name="location" style="width:312px ;">
						<?php
                        $loc=mysqli_query($conn,"select location from incharge");
                        while($row=mysqli_fetch_array($loc))
                        {
                            ?>
                                	<option> <?php echo $row[0]; ?> </option>
                            <?php
                        }
                        ?>
					
				    </select>
				</div>
				<div class="top-w3-agile" style="color: #dfdfdf">Type of Crime
					<select class="form-control" name="type_crime" style="width:312px ;">
						<option>Theft</option>
						<option>Robbery</option>
                        <option>Pick Pocket</option>
                        <option>Murder</option>
                        <option>Rape</option>
                        <option>Molestation</option>
                        <option>Kidnapping</option>
				    </select>
				</div>
					<div class="Top-w3-agile" style="color: #dfdfdf">
					Date Of Crime : &nbsp &nbsp  
						<input type="text" name="d_o_c" placeholder="DD-MM-YYYY" style="background-color:#313131; color:white; width:180px;">
					</div>
					<div class="top-w3-agile" style="color: #dfdfdf">
					Description<br>
						<textarea autocomplete="off" name="description" rows="20" cols="50" placeholder="Description of the incident indetail along with time" onfocusout="f1()" id="desc" required  style="width:312px ;"></textarea>
					</div>
					<input type="submit" value="Submit" name="s"style="width:100px ; border-radius:8% ; margin-left: 108px ;">
				</form>	
			</div>	
		</div>
	</div>	
</div>	

<?php
// Fetch police profile for toast
$u_id = $_SESSION['u_id'];
$profile_result = mysqli_query($conn, "SELECT * FROM user WHERE u_id='$u_id'");
$profile = mysqli_fetch_assoc($profile_result);
?>

<!-- Profile Toast Notification -->
<div aria-live="polite" aria-atomic="true">
  <div class="toast shadow-lg" id="profileToast" style="position: fixed; top: 70px; right: 30px; min-width: 340px; z-index: 9999; display: none; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.25);">
    <div class="toast-header" style="background: linear-gradient(90deg, #007bff 60%, #0056b3 100%); color: #fff; border-bottom: 2px solid #0056b3;">
      <i aria-hidden="true" style="color: #fff; margin-right: 10px;"></i>
      <strong class="mr-auto" style="font-size: 1.3em; letter-spacing: 1px;">User Profile</strong>
    </div>
    <div class="toast-body" style="background: #f8f9fa; padding: 22px 24px 18px 24px;">
      <div style="padding: 10px 0 5px 0; text-align:center;">
        <span class="badge" style="background: linear-gradient(90deg, #007bff 60%, #0056b3 100%); color: #fff; font-size: 1.1em; margin-bottom: 8px; padding: 8px 18px; border-radius: 20px; letter-spacing: 1px;">
          <i></i> ID: <?php echo htmlspecialchars($profile['u_id']); ?>
        </span>
      </div>
      <div style="margin: 18px 0 10px 0;">
        <p style="margin: 0 0 7px 0;"><i class="fa fa-user" style="color:#007bff"></i> <b>Name:</b> <span style="color:#222;"><?php echo htmlspecialchars($profile['u_name']); ?></span></p>
        <p style="margin: 0 0 7px 0;"><i class="fa fa-credit-card" style="color:#007bff"></i> <b>Aadhar:</b> <span style="color:#222;"><?php echo htmlspecialchars($profile['a_no']); ?></span></p>
        <p style="margin: 0 0 7px 0;"><i class="fa fa-map-marker" style="color:#007bff"></i> <b>Address:</b> <span style="color:#222;"><?php echo htmlspecialchars($profile['u_addr']); ?></span></p>
        <p style="margin: 0;"><i class="fa fa-venus-mars" style="color:#007bff"></i> <b>Gender:</b> <span style="color:#222;"><?php echo htmlspecialchars($profile['gen']); ?></span></p>
        <p style="margin: 0;"><i class="fa fa-phone" style="color:#007bff"></i> <b>Mobile:</b> <span style="color:#222;"><?php echo htmlspecialchars($profile['mob']); ?></span></p>
      </div>
      <div style="margin-top: 18px; text-align: center;">
        <a href="logout.php" class="btn btn-danger btn-lg" style="border-radius: 25px; font-size: 1.1em; padding: 8px 28px; box-shadow: 0 2px 8px rgba(220,53,69,0.12); transition: background 0.2s;">
          <i class="fa fa-sign-out" aria-hidden="true"></i> Logout
        </a>
      </div>
    </div>
  </div>
</div>


<div style="position: relative;
   left: 0;
   bottom: 0;
   width: 100%;
   height: 30px;
   background-color: rgba(0,0,0,0.8);
   color: white;
   text-align: center;">
  <h4 style="color: white;">&copy <b>Crime Portal 2025</b></h4>
</div>

<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.js"></script>
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<script>
  $(document).ready(function(){
    $('#profileBtn').click(function(e){
      e.preventDefault();
      $('#profileToast').fadeToggle();
    });

    $('.toast .close').click(function(){
      $(this).closest('.toast').fadeOut();
    });
  });
  
</script>

</body>
</html>