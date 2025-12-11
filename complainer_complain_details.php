<!DOCTYPE html>
<html>
<head>
    
  <?php
    session_start();
    
    $conn = mysqli_connect("localhost", "root", "", "crime_portal");
    if (!$conn) {
        die("Could not connect: " . mysqli_connect_error());
    }
    mysqli_select_db($conn, "crime_portal");
    
    
    if(!isset($_SESSION['x']))
        header("location:userlogin.php");
    
    $u_id=$_SESSION['u_id'];
    $c_id=$_SESSION['cid'];

    // Fetch user name and aadhar
    $stmt = $conn->prepare("SELECT u_name, a_no FROM user WHERE u_id = ?");
    $stmt->bind_param("s", $u_id);
    $stmt->execute();
    $result = $stmt->get_result();
        
    $stmt = $conn->prepare("
    SELECT complaint.c_id, description, inc_status, pol_status, complaint.p_id, police.p_name
    FROM complaint
    NATURAL JOIN user
    LEFT JOIN police ON complaint.p_id = police.p_id
    WHERE c_id = ? AND u_id = ?");
    $stmt->bind_param("ss", $c_id, $u_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $res2=mysqli_query($conn,"select d_o_u,case_update from update_case where c_id='$c_id'");
  ?>

	<title>Complaint Details</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
	<link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">
  
    <body style="background-color: #dfdfdf;">
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
                <ul class="nav navbar-nav navbar-right">
                    <li ><a href="complainer_complain_history.php">View Complaints</a></li>
                    <li class="active" ><a href="complainer_complain_details.php">Complaints Details</a></li>
                    <li><a href="#" id="profileBtn">Profile &nbsp;<i class="fa fa-user" aria-hidden="true"></i></a></li>
                </ul>
            </div>
         </div>
        </nav>
 
        <div style="padding:50px;margin-top:10px;">
            <table class="table table-bordered">
            <thead class="thead-dark" style="background-color: black; color: white;">
                <tr>
                    <th scope="col">Complain Id</th>
                    <th scope="col">Description</th>
                    <th scope="col">Police Name</th>
                    <th scope="col">Police Status</th>
                    <th scope="col">Case Status</th>
                </tr>
            </thead>
            <?php
              while($rows=mysqli_fetch_assoc($result)){
            ?> 
             <tbody style="background-color: white; color: black;">
              <tr>
                <td><?php echo htmlspecialchars($rows['c_id']); ?></td>
                <td><?php echo htmlspecialchars($rows['description']); ?></td>  
                <td><?php echo htmlspecialchars($rows['p_name']); ?></td>  
                <td><?php echo htmlspecialchars($rows['inc_status']); ?></td>     
                <td><?php echo htmlspecialchars($rows['pol_status']); ?></td>
              </tr>
             </tbody>
            <?php
              } 
            ?>
            <?php if (mysqli_num_rows($result) == 0): ?>
            <tr>
              <td colspan="5" style="text-align: center;">No complaint data found.</td>
            </tr>
            <?php endif; ?>
            </table>
        </div>
    
        <div style="padding:50px; margin-top:8px;">
            <table class="table table-bordered">
               <thead class="thead-dark" style="background-color: black; color: white;">
                   <tr>
                        <th scope="col">Date Of Update</th>
                        <th scope="col">Case Update</th>
                   </tr>
               </thead>
            <?php
                while($rows1=mysqli_fetch_assoc($res2)){
             ?> 
                <tbody style="background-color: white; color: black;">
                <tr>
                    <td><?php echo $rows1['d_o_u']; ?></td>
                    <td><?php echo $rows1['case_update']; ?></td>
                </tr>
                </tbody>
            <?php
                } 
            ?>
          
            </table>
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