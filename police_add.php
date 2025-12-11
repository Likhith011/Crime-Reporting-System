<!DOCTYPE html>
<html>
<head>
    <title>Log Police Officer</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet">
    <link href="complainer_page.css" rel="stylesheet" type="text/css" media="all" />

    <?php
    session_start();

    if (!isset($_SESSION['x'])) {
        header("location:inchargelogin.php");
        exit();
    }

    $conn = mysqli_connect("localhost", "root", "", "crime_portal");
    if (!$conn) {
        die("could not connect: " . mysqli_connect_error()); 
    }

    $i_id = $_SESSION['email'];
    $message = '';

    $stmt_location = mysqli_prepare($conn, "SELECT location FROM incharge WHERE i_id = ?");
    if ($stmt_location) {
        mysqli_stmt_bind_param($stmt_location, "s", $i_id);
        mysqli_stmt_execute($stmt_location);
        $result_location = mysqli_stmt_get_result($stmt_location);
        $q2 = mysqli_fetch_assoc($result_location);
        $location = $q2['location'] ?? 'Unknown Location'; 
        mysqli_stmt_close($stmt_location);
    } else {
        $location = 'Error fetching location';
        error_log("Failed to prepare statement for location retrieval: " . mysqli_error($conn));
    }

    if (isset($_POST['s'])) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $p_name = $_POST['police_name'];
            $p_id = $_POST['police_id'];
            $p_email = $_POST['police_email'] ?? ''; 
            $spec = $_POST['police_spec'];
            $p_pass = $_POST['password']; 
            $hashed_password = password_hash($p_pass, PASSWORD_BCRYPT);

            $stmt = mysqli_prepare($conn, "INSERT INTO police (p_name, p_id, p_email, spec, location, p_pass) VALUES (?, ?, ?, ?, ?, ?)");

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssssss", $p_name, $p_id, $p_email, $spec, $location, $hashed_password);
                $res = mysqli_stmt_execute($stmt);

                if (!$res) {
                    if (mysqli_errno($conn) == 1062) {
                        $message = "Police ID '$p_id' already exists. Please use a different ID.";
                    } else {
                        $message = "Error adding police officer: " . mysqli_error($conn);
                    }
                } else {
                    $message = "Police Officer Added Successfully!";
                }
                mysqli_stmt_close($stmt); 
            } else {
                $message = "Database error: Could not prepare statement.";
                error_log("Failed to prepare statement: " . mysqli_error($conn));
            }
        }
    }
    ?>
    
    <script>
    function f1() {
        var pname = document.getElementById("pname").value;
        var pid = document.getElementById("pid").value;
        var pspec = document.getElementById("pspec").value;
        var pas = document.getElementById("pas").value;

        if (pname.length > 0 && pname.trim() === "") {
            document.getElementById("pname").value = "";
            document.getElementById("pname").focus();
            alert("Police Name cannot be just spaces.");
            return;
        }

        if (pid.indexOf(' ') >= 0) {
            document.getElementById("pid").value = "";
            document.getElementById("pid").focus();
            alert("Police ID cannot contain spaces.");
            return;
        }

        if (pspec.length > 0 && pspec.trim() === "") {
            document.getElementById("pspec").value = "";
            document.getElementById("pspec").focus();
            alert("Specialist cannot be just spaces.");
            return;
        }

        if (pas.indexOf(' ') >= 0) {
            document.getElementById("pas").value = "";
            document.getElementById("pas").focus();
            alert("Password cannot contain spaces.");
            return;
        }
    }
    </script>

    <style>
    .login-form form {
        color: #fff; 
    }
    
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
    }

    .login-form input[type="submit"] {
        width: 100%;
        background: #22c7fc;
        color: #fff;
        border: none;
        border-radius: 20px;
        font-size: 20px; 
        cursor: pointer;
    }

    .login-form input[type="submit"]:hover {
        background: #1ba6d8;
    }
    </style>
</head>

<body style="background-size: cover; background-image: url(home_bg1.jpeg); background-position: center;">
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="#"><b>Crime Portal</b></a>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="incharge_view_police.php">Incharge Home</a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a href="police_add.php">Log Police Officer</a></li>
                    <li><a href="Incharge_complain_page.php">Complaint History</a></li>
                    <li><a href="#" id="profileBtn">Profile &nbsp;<i class="fa fa-user" aria-hidden="true"></i></a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="video" style="margin-top: 4%;">
        <div class="center-container">
            <div class="bg-agile">
                <br><br>
                <div class="login-form">
                    <h2>Log Police Officer</h2>
                    <form action="#" method="post">
                        <label>Police Name</label>
                        <input type="text" name="police_name" required id="pname" onfocusout="f1()" placeholder="Police Name"/>
                        <label>Police Id</label>
                        <input type="text" name="police_id" required id="pid" onfocusout="f1()" placeholder="Police ID"/>
                        <label>Police Email</label>
                        <input type="email" name="police_email" required id="pemail" placeholder="Police Email"/>
                        <label>Specialist</label>
                        <input type="text" name="police_spec" required id="pspec" onfocusout="f1()" placeholder="Specialist"/>
                        <label>Location</label>
                        <input type="text" name="location" disabled value="<?php echo htmlspecialchars($location); ?>">
                        <label>Password</label>
                        <input type="password" name="password" required id="pas" onfocusout="f1()" placeholder="Password"/>
                        <input type="submit" name="s" value="Submit" class="btn btn-primary btn-lg">
                    </form>
                </div>
            </div>
        </div>
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

    <!-- Modal Notification -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 10px;">
          <div class="modal-header bg-info text-white">
            <h5 class="modal-title">Notification</h5>
          </div>
          <div class="modal-body" id="modalMessage" style="font-size: 1.1em;"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-info" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-2.1.4.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

    <script>
    $(document).ready(function(){
        $('#profileBtn').click(function(e){
            e.preventDefault();
            $('#profileToast').fadeToggle().delay(4000).fadeOut();
        });
    });
    </script>

    <?php if (!empty($message)): ?>
    <script>
        $(document).ready(function(){
            $('#modalMessage').text("<?php echo $message; ?>");
            $('#statusModal').modal('show');
        });
    </script>
    <?php endif; ?>

</body>
</html>
