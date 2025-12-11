<!DOCTYPE html>
<html>
<head>
	<title>Police pending complain</title>
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
	<link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">
	
     <?php
    session_start();
    if(!isset($_SESSION['x']))
        header("location:policelogin.php");
    $conn=mysqli_connect("localhost","root","","crime_portal");
    if(!$conn)
    {
        die("could not connect".mysqli_error());
    }
    mysqli_select_db($conn,"crime_portal");
    if(isset($_POST['s2']))
    {
      if($_SERVER["REQUEST_METHOD"]=="POST")
      {
       $cid=$_POST['cid'];
       $_SESSION['cid']=$cid;
       $alok=mysqli_query($conn,"SELECT p_id FROM complaint WHERE c_id='$cid'");
       $row = mysqli_fetch_assoc($alok);
       $p_id=$_SESSION['pol'];
     if($row['p_id']==$p_id){
     header("location:police_complainDetails.php");}
     else{
         $message = "Not in your scope";
        echo "<script type='text/javascript'>alert('$message');</script>";
     }
 }
}
    
    $p_id=$_SESSION['pol'];
     $result=mysqli_query($conn,"SELECT c_id,type_crime,d_o_c,location FROM complaint where p_id='$p_id' and pol_status='In Process' order by c_id desc");
    ?>
 <script>
     function f1()
        {
        var sta2=document.getElementById("ciid").value;
        var x2=sta2.indexOf(' ');
      if(sta2!="" && x2>=0){
          document.getElementById("ciid").value="";
          alert("Blank Field Found");
        }
}
</script>
</head>
<body>
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
        <li ><a href="policelogin.php">Police Login</a></li> -->
        <li class="active"><a href="police_pending_complain.php">Police Home</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li class="active" ><a href="police_pending_complain.php">Pending Complaints</a></li>
        <li ><a href="police_complete.php">Completed Complaints</a></li>
        <li><a href="#" id="showProfileToast">Profile &nbsp <i class="fa fa-user" aria-hidden="true"></i></a></li>
      </ul>
    </div>
  </div>
 </nav>

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

<form style="margin-top: 7%; margin-left: 40%;" method="post">
    <input type="text" name="cid" style="width: 250px; height: 30px; background-color:white; color:grey; margin-top:5px;" placeholder="&nbsp Complaint Id" onfocusout="f1()" required id="ciid">
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
        <th scope="col">Location of Crime</th>
        
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
        <td><?php echo $rows['location']; ?></td> 
                  
      </tr>
    </tbody>
    
    <?php
    } 
    ?>
  
</table>
</div>

<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.js"></script>
<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script>
 $(document).ready(function(){
   $('#showProfileToast').click(function(e){
     e.preventDefault();
     $('#profileToast').fadeToggle(); // This toggles show/hide on each click
   });
   $('.toast .close').click(function(){
     $(this).closest('.toast').hide();
   });
 });
</script>
</body>
</html>