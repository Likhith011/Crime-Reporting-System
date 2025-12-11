<!DOCTYPE html>
<html>
<head>
    
    <?php
    session_start();
    if(!isset($_SESSION['x']))
        header("location:inchargelogin.php");
    
    $conn=mysqli_connect("localhost","root","","crime_portal");
    if(!$conn)
    {
        die("could not connect".mysqli_error());
    }
    mysqli_select_db($conn,"crime_portal");
    
    $i_id=$_SESSION['email'];
    $result1=mysqli_query($conn,"SELECT location FROM incharge where i_id='$i_id'");
    $q2=mysqli_fetch_assoc($result1);
    $location=$q2['location'];
    
    if(isset($_POST['s2']))
    {
    if($_SERVER["REQUEST_METHOD"]=="POST")
    {
        $cid=$_POST['cid'];
        
        $_SESSION['cid']=$cid;
        $qu = mysqli_query($conn,"select inc_status,location from complaint where c_id='$cid'");
        $q = mysqli_fetch_assoc($qu);

        if ($q) {
            $inc_st = $q['inc_status'];
            $loc = $q['location'];

            if(strcmp("$loc","$location")!=0)
            {
                $toast ="Case Not of your Location";
                $toast_type = 'error';
            }
            else if(strcmp("$inc_st","Unassigned")==0)
            {   
                header("location:Incharge_complain_details.php");
                
            }
            else{
                header("location:Incharge_complain_details.php");
            }
        } else {
            $toast = "No complaint found with the entered Complaint Id.";
            $toast_type = 'error';
        }
    }
    }
    
    $query="select c_id,type_crime,d_o_c,location,inc_status,p_id from complaint where location='$location' order by c_id desc";
    $result=mysqli_query($conn,$query);  
    ?>

	<title>Incharge Homepage</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">
	
    <script>
     function f1()
        {
          var sta2=document.getElementById("ciid").value;
          var x2=sta2.indexOf(' ');
     if(sta2!="" && x2>=0)
     {
        document.getElementById("ciid").value="";
        alert("Blank Field not Allowed");
      }       
}
</script>
    
</head>
<body style="background-color: #dfdfdf">
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
        <!-- <li ><a href="official_login.php">Official Login</a></li>
        <li ><a href="inchargelogin.php">Incharge Login</a></li> -->
        <li class="active"><a href="Incharge_complain_page.php">Incharge Home</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li class="active"><a href="Incharge_complain_page.php">View Complaints</a></li>
        <li><a href="incharge_view_police.php">Police Officers</a></li>
        <li><a href="#" id="profileBtn">Profile &nbsp;<i class="fa fa-user" aria-hidden="true"></i></a></li>
      </ul>
    </div>
  </div>
 </nav>
    
    <form style="margin-top: 7%; margin-left: 40%;" method="post">
      <input type="text" name="cid" style="width: 250px; height: 30px; background-color:white;" placeholder="&nbsp Complaint Id" id="ciid" onfocusout="f1()" required>
        <div>
      <input class="btn btn-primary" type="submit" value="Search" name="s2" style="margin-top: 10px; margin-left: 11%;">
        </div>
    </form>
    
    
    
 <div style="padding:50px;">
   <table class="table table-bordered">
    <thead class="thead-dark" style="background-color: black; color: white;">
      <tr>
        <th scope="col">Complaint Id</th>
        <th scope="col">Type of Crime</th>
        <th scope="col">Date of Crime</th>
        <th scope="col">Location</th>
        <th scope="col">Complaint Status</th>
          <th scope="col">Police ID</th>
      </tr>
    </thead>

            <?php
              while($rows=mysqli_fetch_assoc($result)){

             ?> 

            <tbody style="background-color: white; color: black;">
      <tr>
        <td><?php echo $rows['c_id'];?></td>
        <td><?php echo $rows['type_crime'];?></td>     
        <td><?php echo $rows['d_o_c'];?></td>
          <td><?php echo $rows['location'];?></td>
          <td><?php echo $rows['inc_status']; ?></td>
          <td><?php echo $rows['p_id']; ?></td>
      </tr>
    </tbody>
    
    <?php
    } 
    ?>
  
</table>
 </div>
    <div style="position: fixed;
   left: 0;
   bottom: 0;
   width: 100%;
   height: 30px;
   background-color: rgba(0,0,0,0.8);
   color: white;
   text-align: center;">
  <h4 style="color: white;">&copy <b>2025 Crime Portal | All Right Reserved</b></h4>
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
          <?php if ($toast_type == 'error'): ?>
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