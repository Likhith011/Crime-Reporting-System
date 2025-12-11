<!DOCTYPE html>
<html>
<head>
    
<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$toast = ""; // For notification messages

$conn = mysqli_connect("localhost", "root", "", "crime_portal");
if (!$conn) {
    die("Could not connect: " . mysqli_connect_error());
}

if (!isset($_SESSION['x'])) {
    header("location:inchargelogin.php");
    exit;
}

$cid = $_SESSION['cid'];
$i_id = $_SESSION['email'];

// Get location of incharge
$result1 = mysqli_query($conn, "SELECT location FROM incharge WHERE i_id='$i_id'");
if (!$result1) {
    die("Query Failed: " . mysqli_error($conn));
}
$q2 = mysqli_fetch_assoc($result1);
$location = $q2['location'];

if (isset($_POST['assign'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        // Check if complaint is already assigned
        $check_query = mysqli_query($conn, "SELECT inc_status FROM complaint WHERE c_id='$cid'");
        $check_data = mysqli_fetch_assoc($check_query);

        if ($check_data && $check_data['inc_status'] === 'Assigned') {
            $toast = "This case has already been assigned!";
        } else {
            $pname = $_POST['police_name'];

            // Get police officer ID
            $res1 = mysqli_query($conn, "SELECT p_id FROM police WHERE p_name='$pname'");
            $q3 = mysqli_fetch_assoc($res1);

            if (!$q3) {
                $toast = "Invalid officer name.";
            } else {
                $pid = $q3['p_id'];

                // Get officer email and name
                $police_q = mysqli_query($conn, "SELECT p_name, p_email FROM police WHERE p_id='$pid'");
                $police_data = mysqli_fetch_assoc($police_q);
                $police_email = $police_data['p_email'];
                $police_name = $police_data['p_name'];

                // Get user email
                $email_q = mysqli_query($conn, "SELECT user.u_id FROM complaint INNER JOIN user ON complaint.a_no = user.a_no WHERE complaint.c_id = '$cid'");
                $user = mysqli_fetch_assoc($email_q);
                $uid = $user['u_id'];

                $user_q = mysqli_query($conn, "SELECT u_name, u_id FROM user WHERE u_id='$uid'");
                $user_data = mysqli_fetch_assoc($user_q);
                $user_email = $user_data['u_id'];
                $user_name = $user_data['u_name'];

                // Mail sending logic
                $mail = new PHPMailer(true);
                $policeMail = new PHPMailer(true);

                try {
                    // === Send User Email ===
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'onlinecrimeportal@gmail.com';
                    $mail->Password = 'wqme eref jtxp wsmf';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('onlinecrimeportal@gmail.com', 'Crime Portal');
                    $mail->addAddress($user_email, $user_name);
                    $mail->isHTML(true);
                    $mail->Subject = 'Complaint Assigned';
                    $mail->Body = "
                        <h3>Dear $user_name,</h3>
                        <p>Your complaint with ID <strong>$cid</strong> has been assigned to Officer <strong>$pname</strong>.</p>
                        <p>Status: <strong>In Process</strong></p>
                        <br><p><b>Regards,<br>Crime Portal</b></p>
                    ";
                    $mail->send();

                    // === Send Police Email ===
                    $policeMail->isSMTP();
                    $policeMail->Host = 'smtp.gmail.com';
                    $policeMail->SMTPAuth = true;
                    $policeMail->Username = 'onlinecrimeportal@gmail.com';
                    $policeMail->Password = 'wqme eref jtxp wsmf';
                    $policeMail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $policeMail->Port = 587;

                    $policeMail->setFrom('onlinecrimeportal@gmail.com', 'Crime Portal');
                    $policeMail->addAddress($police_email, $police_name);
                    $policeMail->isHTML(true);
                    $policeMail->Subject = 'New Complaint Assigned to You';
                    $policeMail->Body = "
                        <h3>Dear Officer $police_name,</h3>
                        <p>You have been assigned a new complaint.</p>
                        <p>Complaint ID: <strong>$cid</strong></p>
                        <p>Please log in to your dashboard to view and take action.</p>
                        <br><p><b>Regards,<br>Crime Portal</b></p>
                    ";
                    $policeMail->send();

                    // === Update Case After Emails Sent ===
                    $res = mysqli_query($conn, "UPDATE complaint SET inc_status='Assigned', pol_status='In Process', p_id='$pid' WHERE c_id='$cid'");
                    if ($res) {
                        $toast = "Case assigned and emails sent successfully.";
                    } 
                } catch (Exception $e) {
                    $toast = "Case not assigned. Email sending failed: " . $e->getMessage();
                }
            }
        }
    }
}

// Fetch complaint details for the current complaint ID
$complaint_query = "SELECT c.*, p.p_name 
    FROM complaint c 
    LEFT JOIN police p ON c.p_id = p.p_id 
    WHERE c.c_id = '$cid'";
$result = mysqli_query($conn, $complaint_query);
if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}
?>

	<title>Assign Police</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

</head>
<body>
	<nav class="navbar navbar-default navbar-fixed-top">
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
      
      <ul class="nav navbar-nav navbar-right">
        <li ><a href="Incharge_complain_page.php">View Complaints</a></li>
        <li class="active" ><a href="Incharge_complain_details.php">Complaints Details</a></li>
        <li><a href="#" id="profileBtn">Profile &nbsp;<i class="fa fa-user" aria-hidden="true"></i></a></li>
      </ul>
    </div>
  </div>
 </nav>
    
<div style="padding:50px; margin-top:10px;">
   <table class="table table-bordered">
    <thead class="thead-dark" style="background-color: black; color: white;">
    <tr>
      <th scope="col">Complaint Id</th>
      <th scope="col">Type of Crime</th>
      <th scope="col">Date of Crime</th>
      <th scope="col">Description</th>
      <th scope="col">Assigned Police</th>
    </tr>
       </thead>
      <?php
              while($rows=mysqli_fetch_assoc($result)){
             ?> 
       <tbody style="background-color: white; color: black;">
    <tr>
        
      <td><?php echo $rows['c_id']; ?></td>
      <td><?php echo $rows['type_crime']; ?></td>
      <td><?php echo $rows['d_o_c']; ?></td>
      <td><?php echo $rows['description']; ?></td>
      <td><?php echo $rows['p_name'] ? htmlspecialchars($rows['p_name']) : "<span class='text-muted'>Not Assigned</span>"; ?></td>
    </tr>
       </tbody>
       <?php
} 
?>
          
</table>
 </div>
 <div>  
<form method="post">
    <select class="form-control" name="police_name" style="margin-left:40%; width:250px;">
        <?php
        // Check database connection
        if (!$conn) {
            die("Database connection failed: " . mysqli_connect_error());
        }

        if (!isset($location) || empty($location)) {
            die("Error: Location is not set or empty.");
        }
        echo "Debug - Location: " . htmlspecialchars($location) . "<br>";

        $stmt = $conn->prepare("SELECT p_name FROM police WHERE location = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $location);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            echo "<option>No records found</option>";
        } else {
            while ($row = $result->fetch_assoc()) {
                echo "<option>" . htmlspecialchars($row['p_name']) . "</option>";
            }
        }

        $stmt->close();
        ?>
    </select>
    <input type="submit" name="assign" value="Assign Case" class="btn btn-primary" style="margin-top:10px; margin-left:45%;">
</form>
 </div>
 
<div style="position: fixed;
   left: 0;
   bottom: 0;
   width: 100%;
   height: 30px;
   background-color: rgba(0,0,0,0.8);
   color: white;
   text-align: center;">
  <h4 style="color: white;">&copy <b>Crime Portal 2025</b></h4>
</div>

<!-- Toast Notification for Profile -->
<?php
if (isset($_SESSION['email'])) {
    $i_id = $_SESSION['email'];
    $profile_result = mysqli_query($conn, "SELECT * FROM incharge WHERE i_id='$i_id'");
    $profile = mysqli_fetch_assoc($profile_result);
} else {
    // Redirect to login or show an error
    $profile = null;
    echo "<script>window.location.href='inchargelogin.php';</script>";
    exit();
}
?>
<div aria-live="polite" aria-atomic="true">
  <div class="toast shadow-lg" id="profileToast" style="position: fixed; top: 70px; right: 30px; min-width: 380px; min-height: 270px; z-index: 9999; display: none; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 32px rgba(0,0,0,0.25);">
    <div class="toast-header" style="background: linear-gradient(90deg, #007bff 60%, #0056b3 100%); color: #fff; border-bottom: 2px solid #0056b3;">
      <i aria-hidden="true" style="color: #fff; margin-right: 10px;"></i>
      <strong class="mr-auto" style="font-size: 1.3em; letter-spacing: 1px;">Incharge Profile</strong>
    </div>
    <div class="toast-body" style="background: #f8f9fa; padding: 22px 24px 18px 24px;">
      <?php if ($profile): ?>
        <div style="padding: 10px 0 5px 0; text-align:center;">
          <span class="badge" style="background: linear-gradient(90deg, #007bff 60%, #0056b3 100%); color: #fff; font-size: 1.1em; margin-bottom: 8px; padding: 8px 18px; border-radius: 20px; letter-spacing: 1px;">
            <i></i> ID: <?php echo htmlspecialchars($profile['i_id']); ?>
          </span>
        </div>
        <div style="margin: 18px 0 10px 0;">
          <p style="margin: 0 0 7px 0;"><i class="fa fa-user" style="color:#007bff"></i> <b>Name:</b> <span style="color:#222;"><?php echo htmlspecialchars($profile['i_name']); ?></span></p>
          <p style="margin: 0 0 7px 0;"><i class="fa fa-envelope" style="color:#007bff"></i> <b>Email:</b> <span style="color:#222;"><?php echo htmlspecialchars($profile['i_email']); ?></span></p>
          <p style="margin: 0;"><i class="fa fa-map-marker" style="color:#007bff"></i> <b>Location:</b> <span style="color:#222;"><?php echo htmlspecialchars($profile['location']); ?></span></p>
        </div>
        <div style="margin-top: 18px; text-align: center;">
          <a href="inc_logout.php" class="btn btn-danger btn-lg" style="border-radius: 25px; font-size: 1.1em; padding: 8px 28px; box-shadow: 0 2px 8px #dc3545; transition: background 0.2s;">
            <i class="fa fa-sign-out" aria-hidden="true"></i> Logout
          </a>
        </div>
      <?php else: ?>
        <div style="color: red; text-align: center; font-weight: bold; padding: 20px;">
          Profile information not found.
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php if (!empty($toast)): ?>
<!-- Stylish Modal Alert -->
<div class="modal fade" id="toastModal" tabindex="-1" role="dialog" aria-labelledby="toastLabel">
  <div class="modal-dialog" role="document" style="margin-top: 180px;">
    <div class="modal-content" style="border-radius: 18px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.3);">
      
      <!-- Header with Icon -->
      <div class="modal-header" style="background: linear-gradient(90deg, <?php echo (stripos($toast, 'already') || stripos($toast, 'Invalid') || stripos($toast, 'failed')) ? '#d9534f' : '#5cb85c'; ?>, #333); color: white; text-align: center; border-bottom: none;">
        <h4 class="modal-title" style="font-weight: 600; font-size: 20px;">
          <?php if (stripos($toast, 'already') || stripos($toast, 'Invalid') || stripos($toast, 'failed')): ?>
            <i class="fa fa-times-circle" style="margin-right: 8px;"></i> Error
          <?php else: ?>
            <i class="fa fa-check-circle" style="margin-right: 8px;"></i> Success
          <?php endif; ?>
        </h4>
      </div>

      <!-- Body -->
      <div class="modal-body text-center" style="padding: 30px 20px;">
        <p style="font-size: 16px; color: #333; margin-bottom: 0;"><?php echo $toast; ?></p>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

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

  $(document).ready(function() {
    <?php if (!empty($toast)): ?>
      $('#toastModal').modal('show');
      setTimeout(function() {
        $('#toastModal').modal('hide');
      }, 3000);
    <?php endif; ?>
  });
</script>

</body>
</html>