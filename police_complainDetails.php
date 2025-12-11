<!DOCTYPE html>
<html>
<head>
<?php
session_start();
if (!isset($_SESSION['x'])) {
    header("location:policelogin.php");
    exit();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$conn = mysqli_connect("localhost", "root", "", "crime_portal");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$cid = $_SESSION['cid'];
$p_id = $_SESSION['pol'];

$query = "SELECT c_id, type_crime, d_o_c, description, mob, u_addr 
          FROM complaint 
          NATURAL JOIN user 
          WHERE c_id='$cid' AND p_id='$p_id'";
$result = mysqli_query($conn, $query);

$status_result = mysqli_query($conn, "SELECT pol_status FROM complaint WHERE c_id='$cid'");
$status_row = mysqli_fetch_assoc($status_result);
$case_closed = ($status_row['pol_status'] === 'ChargeSheet Filed');

// Update case status and send email
if (isset($_POST['status']) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $upd = $_POST['update'];

    // Check for duplicate update
    $check = mysqli_query($conn, "SELECT * FROM update_case WHERE c_id='$cid' AND case_update='$upd'");
    if (mysqli_num_rows($check) == 0) {
        $user_query = mysqli_query($conn, 
            "SELECT user.u_id, complaint.type_crime, complaint.description 
             FROM complaint 
             JOIN user ON complaint.a_no = user.a_no 
             WHERE complaint.c_id='$cid'");

        if ($user_row = mysqli_fetch_assoc($user_query)) {
            $to = $user_row['u_id']; // Assumes this is an email
            $subject = "Your Complaint (ID: $cid) Has Been Updated";

            $body = "Dear User,\n\nYour complaint has been updated.\n\n" .
                    "Complaint ID: $cid\n" .
                    "Type of Crime: " . $user_row['type_crime'] . "\n" .
                    "Description: " . $user_row['description'] . "\n" .
                    "Latest Update: $upd\n\nThank you,\nCrime Portal";

            // Send email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'onlinecrimeportal@gmail.com'; // your Gmail
                $mail->Password = 'wqme eref jtxp wsmf';     // app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('onlinecrimeportal@gmail.com', 'Crime Portal');
                $mail->addAddress($to);
                $mail->Subject = $subject;
                $mail->Body    = $body;

                $mail->send();
                mysqli_query($conn, "INSERT INTO update_case(c_id, case_update) VALUES('$cid', '$upd')");
                $_SESSION['update_msg'] = "Complaint update successful and email sent.";
            } catch (Exception $e) {
                $_SESSION['update_msg'] = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    } else {
        $_SESSION['already_alert'] = "This update has already been recorded.";
    }

    header("Location: police_complainDetails.php");
    exit();
}

// Close case
if (isset($_POST['close']) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $up = $_POST['final_report'];
    mysqli_query($conn, "INSERT INTO update_case(c_id, case_update) VALUES('$cid', '$up')");
    mysqli_query($conn, "UPDATE complaint SET pol_status='ChargeSheet Filed' WHERE c_id='$cid'");
}

$res2 = mysqli_query($conn, "SELECT d_o_u, case_update FROM update_case WHERE c_id='$cid'");
?>

<title>Log Complaint Status</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.6.2/css/bootstrap.min.css">
<link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
<script>
function f1() {
    var sta2 = document.getElementById("ciid").value;
    if (sta2 === "" || sta2.indexOf(' ') >= 0) {
        document.getElementById("ciid").value = "";
        alert("Blank Field Not Allowed");
    }
}
</script>
<style>
  @media (max-width: 576px) {
    #profileToast, #statusToast {
      width: 95%;
      right: 2.5%;
    }
  }

  body {
      background-color: #f8f9fa;
  }
  .toast {
      border-radius: 14px;
      overflow: hidden;
  }
  .toast-header {
      background: linear-gradient(90deg, #28a745 0%, #218838 100%);
      color: #fff !important;
      font-weight: bold;
      border-bottom: none;
  }
  .toast-profile {
      background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
  }
  .toast-body {
      padding: 15px;
  }
</style>
</head>
<body>

<!-- Toast-based Status Message -->
<?php if (!empty($_SESSION['update_msg']) || !empty($_SESSION['already_alert'])): 
    $message = $_SESSION['update_msg'] ?? $_SESSION['already_alert'];

    // Default style (success)
    $headerText = "Success";
    $icon = "fa-check-circle";
    $gradient = "linear-gradient(90deg,#28a745,#1e7e34)"; // green

    // If message contains keywords, switch to error style
    if (stripos($message, 'error') !== false || stripos($message, 'not') !== false || stripos($message, 'fail') !== false) {
        $headerText = "Error";
        $icon = "fa-times-circle";
        $gradient = "linear-gradient(90deg,#dc3545,#a71d2a)"; // red
    }
?>
<!-- Centered Status Card -->
<div id="statusCard" 
     style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
            background: #fff; border-radius: 16px; box-shadow: 0 6px 18px rgba(0,0,0,0.25);
            min-width: 340px; max-width: 500px; text-align: center; z-index: 20000;
            opacity: 0; transition: opacity 0.6s ease; margin-top: -30px;">
    <!-- Header -->
    <div style="background: <?= $gradient ?>;
                padding: 15px; border-top-left-radius: 16px; border-top-right-radius: 16px;
                color: #fff; font-weight: bold; font-size: 1.2rem;">
        <i class="fa <?= $icon ?>"></i> <?= $headerText ?>
    </div>

    <!-- Body -->
    <div style="padding: 20px; color: #333;">
        <?= htmlspecialchars($message) ?>
    </div>
</div>

<script>
  const statusCard = document.getElementById('statusCard');
  if (statusCard) {
    // Fade In
    setTimeout(() => { statusCard.style.opacity = "1"; }, 100);

    // Fade Out after 4s
    setTimeout(() => { statusCard.style.opacity = "0"; }, 4000);

    // Remove element after fade out
    setTimeout(() => { if (statusCard) statusCard.remove(); }, 4600);
  }
</script>

<?php unset($_SESSION['update_msg'], $_SESSION['already_alert']); endif; ?>

<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <a class="navbar-brand" href="home.php"><b>Crime Portal</b></a>
    </div>
    <div class="collapse navbar-collapse" id="navbar">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="police_pending_complain.php">View Complaints</a></li>
        <li class="active"><a href="police_complainDetails.php">Complaint Details</a></li>
        <li><a href="#" id="showProfileToast">Profile &nbsp <i class="fa fa-user" aria-hidden="true"></i></a></li>
      </ul>
    </div>
  </div>
</nav>

<div style="padding:60px;">
  <table class="table table-bordered">
    <thead style="background-color: black; color: white;">
      <tr>
        <th>Complaint Id</th>
        <th>Type of Crime</th>
        <th>Date of Crime</th>
        <th>Description</th>
        <th>Complainant Mobile</th>
        <th>Complainant Address</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($rows = mysqli_fetch_assoc($result)) { ?>
        <tr>
          <td><?php echo $rows['c_id']; ?></td>
          <td><?php echo $rows['type_crime']; ?></td>
          <td><?php echo $rows['d_o_c']; ?></td>
          <td><?php echo htmlspecialchars($rows['description']); ?></td>
          <td><?php echo $rows['mob']; ?></td>
          <td><?php echo $rows['u_addr']; ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<div style="padding:50px;">
  <table class="table table-bordered">
    <thead style="background-color: black; color: white;">
      <tr>
        <th>Date Of Update</th>
        <th>Case Update</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($rows1 = mysqli_fetch_assoc($res2)) { ?>
        <tr>
          <td><?php echo $rows1['d_o_u']; ?></td>
          <td><?php echo $rows1['case_update']; ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<?php if (!$case_closed): ?>
<div style="display: flex; padding: 20px;">
  <div style="flex: 1; background-color: #dcdcdc; padding: 20px;">
    <form method="post">
      <h5 style="text-align: center;"><b>Complaint ID</b></h5>
      <input type="text" name="cid" value="<?php echo $cid ?>" disabled style="margin: auto; display: block; width: 100px;">
      <select class="form-control" name="update" style="margin: 20px auto; width: 200px;">
        <option value="" disabled selected>Select Case Status</option>
        <option>Criminal Identified</option>
        <option>Criminal Arrested</option>
        <option>Criminal is under Interogation</option>
        <option>Criminal Accepted the Crime</option>
        <option>Evidence Collected</option>
        <option>Witnesses Identified</option>
        <option>Witnesses Interrogated</option>
        <option>Witnesses Accepted the Crime</option>
        <option>FIR Filed</option>
        <option>Criminal has been produced to the court</option>
        <option>Criminal Released on Bail</option>
      </select>
      <input type="submit" name="status" class="btn btn-primary" value="Update Case Status" style="display: block; margin: 10px auto;">
    </form>
  </div>

  <div style="flex: 1; background-color: #dfdfdf; padding: 20px;">
    <form method="post">
      <textarea name="final_report" cols="40" rows="5" placeholder="Final Report" id="ciid" onfocusout="f1()" required style="width: 100%;"></textarea>
      <input type="submit" name="close" class="btn btn-danger" value="Close Case" onclick="return confirm('Are you sure you want to close this case?');" style="margin-top: 10px;">
    </form>
  </div>
</div>
<?php else: ?>
  <div style="text-align:center; padding:20px;">
    <h4><span class="label label-success">This case is closed. No further updates allowed.</span></h4>
  </div>
<?php endif; ?>

<?php
// Fetch police profile for toast
$p_id = $_SESSION['pol'];
$profile_result = mysqli_query($conn, "SELECT * FROM police WHERE p_id='$p_id'");
$profile = mysqli_fetch_assoc($profile_result);
?>
<div aria-live="polite" aria-atomic="true">
  <div class="toast shadow-lg" id="profileToast" style="position: fixed; top: 70px; right: 30px; min-width: 340px; z-index: 9999; display: none; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.25);">
    <div class="toast-header" style="background: linear-gradient(90deg, #007bff 60%, #0056b3 100%); color: #fff; border-bottom: 2px solid #0056b3;">
      <i class="fa fa-user-circle fa-2x mr-2" aria-hidden="true" style="color: #fff; margin-right: 10px;"></i>
      <strong class="mr-auto" style="font-size: 1.3em; letter-spacing: 1px;">Police Profile</strong>
    </div>
    <div class="toast-body" style="background: #f8f9fa; padding: 22px 24px 18px 24px;">
      <div style="padding: 10px 0 5px 0; text-align:center;">
        <span class="badge" style="background: linear-gradient(90deg, #007bff 60%, #0056b3 100%); color: #fff; font-size: 1.1em; margin-bottom: 8px; padding: 8px 18px; border-radius: 20px; letter-spacing: 1px;">
          <i class="fa fa-id-badge"></i> ID: <?php echo htmlspecialchars($profile['p_id']); ?>
        </span>
      </div>
      <div style="margin: 18px 0 10px 0;">
        <p style="margin: 0 0 7px 0;"><i class="fa fa-user" style="color:#007bff"></i> <b>Name:</b> <span style="color:#222;"><?php echo htmlspecialchars($profile['p_name']); ?></span></p>
        <p style="margin: 0 0 7px 0;"><i class="fa fa-envelope" style="color:#007bff"></i> <b>Email:</b> <span style="color:#222;"><?php echo htmlspecialchars($profile['p_email']); ?></span></p>
        <p style="margin: 0 0 7px 0;"><i class="fa fa-shield" style="color:#007bff"></i> <b>Specialization:</b> <span style="color:#222;"><?php echo htmlspecialchars($profile['spec']); ?></span></p>
        <p style="margin: 0;"><i class="fa fa-map-marker" style="color:#007bff"></i> <b>Location:</b> <span style="color:#222;"><?php echo htmlspecialchars($profile['location']); ?></span></p>
      </div>
      <div style="margin-top: 18px; text-align: center;">
        <a href="p_logout.php" class="btn btn-danger btn-lg" style="border-radius: 25px; font-size: 1.1em; padding: 8px 28px; box-shadow: 0 2px 8px rgba(220,53,69,0.12); transition: background 0.2s;">
          <i class="fa fa-sign-out" aria-hidden="true"></i> Logout
        </a>
      </div>
    </div>
  </div>
</div>

<div style="width: 100%; background-color: rgba(0,0,0,0.8); color: white; text-align: center; padding: 10px 0;">
  <h4>&copy; <b>Crime Portal 2025</b></h4>
</div>

<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.js"></script>
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script>
 $(document).ready(function(){
   $('#showProfileToast').click(function(e){
     e.preventDefault();
     $('#profileToast').fadeToggle(); 
   });
 });
</script>
</body>
</html>
