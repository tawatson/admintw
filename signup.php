<?php
// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
$username = $password = $confirm_password = $full_name = "";
$username_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate username
    if(empty(trim($_POST["email"]))){
        $username_err = "Please fill out all fields (Email).";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM wa_users WHERE email = :email";

        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(':email', $param_username, PDO::PARAM_STR);

            // Set parameters
            $param_username = trim($_POST["email"]);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "This email is associated with an account already. Please log in.";
                } else{
                    $username = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        unset($stmt);
    }

    // Validate password
    if(empty(trim($_POST['password']))){
        $password_err = "Please enter a password.";
    } elseif(strlen(trim($_POST['password'])) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST['password']);
    }

    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = 'Please confirm password.';
    } else{
        $confirm_password = trim($_POST['confirm_password']);
        if($password != $confirm_password){
            $confirm_password_err = 'Password did not match.';
        }
    }

    // Validate name
    if(empty(trim($_POST['full_name']))){
        $username_err = "Please fill out all fields (Name).";
    } else {
        $full_name = trim($_POST['full_name']);
    }

    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){

        // Prepare an insert statement
        $sql = "INSERT INTO wa_users (email, password, full_name) VALUES (:email, :password, :full_name)";

        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(':email', $param_username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $param_password, PDO::PARAM_STR);
            $stmt->bindParam(':full_name', $full_name, PDO::PARAM_STR);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                header("location: signin.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }

        // Close statement
        unset($stmt);
    } else {
      echo "<script>console.log('Error: ".$username_err. " ".$password_err." ".$confirm_password_err."'');</script>";
    }

    // Close connection
    unset($pdo);
}  else {
  session_start();
  if(isset($_SESSION['username'])){
    header("Location: index.php");
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
  <title>Sign Up</title>
  <style>
    #loader {
      transition: all .3s ease-in-out;
      opacity: 1;
      visibility: visible;
      position: fixed;
      height: 100vh;
      width: 100%;
      background: #fff;
      z-index: 90000
    }

    #loader.fadeOut {
      opacity: 0;
      visibility: hidden
    }

    .spinner {
      width: 40px;
      height: 40px;
      position: absolute;
      top: calc(50% - 20px);
      left: calc(50% - 20px);
      background-color: #333;
      border-radius: 100%;
      -webkit-animation: sk-scaleout 1s infinite ease-in-out;
      animation: sk-scaleout 1s infinite ease-in-out
    }

    @-webkit-keyframes sk-scaleout {
      0% {
        -webkit-transform: scale(0)
      }
      100% {
        -webkit-transform: scale(1);
        opacity: 0
      }
    }

    @keyframes sk-scaleout {
      0% {
        -webkit-transform: scale(0);
        transform: scale(0)
      }
      100% {
        -webkit-transform: scale(1);
        transform: scale(1);
        opacity: 0
      }
    }
  </style>
  <link href="static/style.css" rel="stylesheet">
</head>

<body class="app">
  <div id="loader">
    <div class="spinner"></div>
  </div>
  <script>
    window.addEventListener('load', () => {
      const loader = document.getElementById('loader');
      setTimeout(() => {
        loader.classList.add('fadeOut');
      }, 300);
    });
  </script>
  <div class="peers ai-s fxw-nw h-100vh">
    <div class="peer peer-greed h-100 pos-r bgr-n bgpX-c bgpY-c bgsz-cv" style="background-image:url(https://terrenceawatson.com/2018/assets/img/coffee.jpg)">
      <div class="pos-a centerXY">
        <div class="bgc-white bdrs-50p pos-r" style="width:300px;height:300px"><img class="pos-a centerXY" src="static/assets/static/images/logo.png" alt="" style="width:80%;"></div>
      </div>
    </div>
    <div class="col-12 col-md-4 peer pX-40 pY-80 h-100 bgc-white scrollable pos-r" style="min-width:320px">
      <h4 class="fw-300 c-grey-900 mB-40">Register</h4>
      <form method="post">
        <div class="form-group"><label class="text-normal text-dark">Full Name</label> <input type="text" class="form-control" name="full_name" placeholder="John Doe"></div>
        <div class="form-group">
          <label class="text-normal text-dark">Email Address</label>
          <input name="email" type="email" class="form-control" placeholder="name@email.com"></div>
        <div class="form-group"><label class="text-normal text-dark">Password</label> <input type="password" name="password" class="form-control" placeholder="Password"></div>
        <div class="form-group"><label class="text-normal text-dark">Confirm Password</label> <input type="password" name="confirm_password" class="form-control" placeholder="Password"></div>
        <div class="form-group"><input type="submit" class="btn btn-primary" value="Register"></div>
        <div class="form-group">
          <a class="btn btn-default text-normal text-dark" href="signin.php">Login</a>
        </div>
      </form>
    </div>
  </div>
  <script type="text/javascript" src="static/vendor.js"></script>
  <script type="text/javascript" src="static/bundle.js"></script>
</body>

</html>
