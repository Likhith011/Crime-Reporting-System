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
        $result1=mysqli_query($conn,"SELECT a_no FROM user where u_id='$u_id'");
      
        $q2=mysqli_fetch_assoc($result1);
        $a_no=$q2['a_no'];

    $uname_res = mysqli_query($conn, "SELECT u_name FROM user WHERE u_id='$u_id'");
    $uname_row = mysqli_fetch_assoc($uname_res);
    $u_name = $uname_row['u_name'];
    
    if(isset($_POST['s2']))
    {
        if($_SERVER["REQUEST_METHOD"]=="POST")
        {
            
            $cid=$_POST['cid'];

            $_SESSION['cid']=$cid;
            
            $resu=mysqli_query($conn,"SELECT a_no FROM complaint where c_id='$cid'");
            $qn=mysqli_fetch_assoc($resu);
                
            
           if($qn['a_no']==$q2['a_no'])
           {
                header("location:complainer_complain_details.php"); 
           }
            else
            {
              $message = "Not Your Case";
              echo "<script type='text/javascript'>alert('$message');</script>";
            }
        }
    }
    
    
    
    $query="select c_id,type_crime,d_o_c,location from complaint where a_no='$a_no' order by c_id desc";
    $result=mysqli_query($conn,$query);  
    ?>
    
	<title>Complaint History</title>
    
	  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
	  <link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

    <script>
     function f1()
        {
          
            var sta2=document.getElementById("ciid").value;
            var x2=sta2.indexOf(' ');
  
            if(sta2!="" && x2>=0)
            {
                  document.getElementById("ciid").value="";
                  alert("Space Not Allowed");
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
                        <!-- <li ><a href="complainer_page.php">User Login</a></li> -->
                        <li class="active"><a href="complainer_page.php">User Home</a></li>
                    </ul>
   
                    <ul class="nav navbar-nav navbar-right">
                        <li ><a href="complainer_page.php">Log New Complain</a></li>
                        <li class="active"><a href="complainer_complain_history.php">Complaint History</a></li>
                        <li><a href="#" id="profileBtn">Profile &nbsp;<i class="fa fa-user" aria-hidden="true"></i></a></li>
                    </ul>
                  </div>
              </div>
        </nav>


    <div>
        <form style="float: right; margin-right: 100px; margin-top: 65px;" method="post">
            <input type="text" name="cid" style="width: 250px; height: 30px; color: black;" placeholder="&nbsp Complain Id" id="ciid" onfocusout="f1()" required>
            <input class="btn btn-primary btn-sm" type="submit" value="Search" style="margin-top:2px; margin-left:20px;" name="s2">
        </form>
    </div>


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