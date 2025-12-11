<!DOCTYPE html>
<html>
<head>
    <title>police station log </title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">
    <link href="complainer_page.css" rel="stylesheet" type="text/css" media="all" />
<?php
session_start();
if(!isset($_SESSION['x']))
    header("location:headlogin.php");

if(isset($_POST['s'])){
    $con = mysqli_connect('localhost','root','','crime_portal');
    if(!$con) {
        die('could not connect: '.mysqli_error($con));
    }
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $loc = $_POST['location'];
        $i_name = $_POST['incharge_name'];
        $i_id = $_POST['incharge_id'];
        $i_email = $_POST['incharge_email'];
        $i_pass = $_POST['password']; 

        
        $reg = "INSERT INTO incharge (i_id, i_name, i_email, location, i_pass) 
                VALUES ('$i_id', '$i_name', '$i_email', '$loc', '$i_pass')";

        mysqli_select_db($conn,"crime_portal");
        $res = mysqli_query($conn, $reg);
        if(!$res) {
            $message1 = "User Already Exist or Error in Data";
            echo "<script type='text/javascript'>alert('$message1');</script>";
        } else {
            $message = "Police Station Added Successfully";
            echo "<script type='text/javascript'>alert('$message');</script>";
        }
    }
}
?>
<script>
function f1() {
    var sta = document.getElementById("station").value;
    var sta1 = document.getElementById("iname").value;
    var sta2 = document.getElementById("iid").value;
    var sta3 = document.getElementById("pas").value;
    var x = sta.trim();
    var x2 = sta2.indexOf(' ');
    var x1 = sta1.trim();
    var x3 = sta3.indexOf(' ');
    if(sta!="" && x==""){
        document.getElementById("station").value="";
        document.getElementById("station").focus();
        alert("Space Not Allowed");
    }
    else if(sta1!="" && x1==""){
        document.getElementById("iname").value="";
        document.getElementById("iname").focus();
        alert("Space Not Allowed");
    }
    else if(sta2!="" && x2>=0){
        document.getElementById("iid").value="";
        document.getElementById("iid").focus();
        alert("Space Not Allowed");
    }
    else if(sta3!="" && x3>=0){
        document.getElementById("pas").value="";
        document.getElementById("pas").focus();
        alert("Space Not Allowed");
    }
}
</script>
<style>
.login-form input[type="text"],
.login-form input[type="email"],
.login-form input[type="password"] {
    width: 100%;
    padding: 12px 15px;
    margin: 4px 0 15px 0;
    display: block;
    border: none;
    border-radius: 5px;
    background: #222;
    color: #fff;
    font-size: 14px; 
    outline: none;
    box-shadow: none;
    box-sizing: border-box;
    opacity: 0.9;
}

.login-form input[type="text"]::placeholder,
.login-form input[type="email"]::placeholder,
.login-form input[type="password"]::placeholder {
    color: #fff;
    opacity: 0.7;
    font-size: 14px;
}

.login-form input[type="submit"] {
    width: 40%;
    background: #22c7fc;
    color: #fff;
    border: none;
    border-radius: 20px;
    font-size: 20px; 
    cursor: pointer;
    transition: background 0.3s;
}

.login-form input[type="submit"]:hover {
    background: #1ba6d8;
}

.login-form form {
    color: #fff; 
}
</style>
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
        <!-- <li ><a href="official_login.php">HQ Login</a></li> -->
        <li><a href="headHome.php">HQ Home</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li class="active"><a href="police_station_add.php">Log Police Station</a></li>
        <li> <a href="h_logout.php">Logout &nbsp <i class="fa fa-sign-out" aria-hidden="true"></i></a></li>
      </ul>
    </div>
  </div>
 </nav>
<div class="video" style="margin-top: -2%"> 
    <div class="center-container">
         <div class="bg-agile">
            <br><br>
            <div class="login-form"><p>
                <h2>Log Police Station</h2><br>
      <form method="post">
        <label style="color:#fff;">Police Station Location</label>
        <input type="text" name="location" placeholder="Station Location" required="" id="station" onfocusout="f1()"/>
        <label style="color:#fff;">Incharge Name</label>
        <input type="text" name="incharge_name" placeholder="Incharge Name" required="" id="iname" onfocusout="f1()"/>
        <label style="color:#fff;">Incharge Id</label>
        <input type="text" name="incharge_id" placeholder="Incharge Id" required="" id="iid" onfocusout="f1()"/>
        <label style="color:#fff;">Incharge Email</label>
        <input type="email" name="incharge_email" placeholder="Incharge Email" id="iemail" onfocusout="f1()"/>
        <label style="color:#fff;">Password</label>
        <input type="password" name="password" placeholder="Password" required="" id="pas" onfocusout="f1()"/>
        <input type="submit" value="Submit" name="s" style="margin-left: 86px;">
      </form>    
            </div>    
        </div>
    </div>    
</div>    
 <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.js"></script>
 <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>