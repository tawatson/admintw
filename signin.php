<?php
// Include config file
require_once 'config.php';


// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if username is empty
    if(empty(trim($_POST["email"]))){
        $username_err = 'Please enter email.';
    } else{
        $username = trim($_POST["email"]);
    }

    // Check if password is empty
    if(empty(trim($_POST['password']))){
        $password_err = 'Please enter your password.';
    } else{
        $password = trim($_POST['password']);
    }

    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT email, password FROM wa_users WHERE email = :email";

        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(':email', $param_username, PDO::PARAM_STR);

            // Set parameters
            $param_username = trim($_POST["email"]);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Check if username exists, if yes then verify password
                if($stmt->rowCount() == 1){
                    if($row = $stmt->fetch()){
                        $hashed_password = $row['password'];
                        if(password_verify($password, $hashed_password)){
                            /* Password is correct, so start a new session and
                            save the username to the session */
                            if($_POST['remember_me']){
                              $year = time() + 31536000;
                              setcookie('remember_me', $_POST['email'], $year);
                              session_start();
                              $_SESSION['username'] = $username;
                              header("location: index.php");
                            } else {

                            session_start();
                            $_SESSION['username'] = $username;
                            header("location: index.php");
                          }
                        } else{
                            // Display an error message if password is not valid
                            $password_err = 'The password you entered was not valid.';
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = 'No account found with that username.';
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        unset($stmt);
    }

    // Close connection
    unset($pdo);
} else {
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
  <title>Sign In</title>
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
    <div class="d-n@sm- peer peer-greed h-100 pos-r bgr-n bgpX-c bgpY-c bgsz-cv" style="background-image:url(https://terrenceawatson.com/2018/assets/img/coffee.jpg)">
      <div class="pos-a centerXY">
        <div class="bgc-white bdrs-50p pos-r" style="width:300px;height:300px"><img class="pos-a centerXY" src="static/assets/static/images/logo.png" alt="" style="width:80%;"></div>
      </div>
    </div>
    <div class="col-12 col-md-4 peer pX-40 pY-80 h-100 bgc-white scrollable pos-r" style="min-width:320px">
      <h4 class="fw-300 c-grey-900 mB-40">Login</h4>
      <? if(isset($username_err) || isset($password_err)){echo $username_err; echo $password_err;}?>
      <form method="post">
        <div class="form-group"><label class="text-normal text-dark">Email</label> <input type="email" name="email" class="form-control" placeholder="name@email.com" value="<?php if(isset($_COOKIE['remember_me'])){echo $_COOKIE['remember_me'];} ?>"></div>
        <div class="form-group"><label class="text-normal text-dark">Password</label> <input type="password" name="password" class="form-control" placeholder="Password"></div>
        <div class="form-group">
          <div class="peers ai-c jc-sb fxw-nw">
            <div class="peer">
              <div class="checkbox checkbox-circle checkbox-info peers ai-c"><input type="checkbox" id="inputCall1" name="remember_me" class="peer" <?php if(isset($_COOKIE['remember_me'])) { echo 'checked="checked"';} ?>> <label for="inputCall1" class="peers peer-greed js-sb ai-c"><span class="peer peer-greed">Remember Me</span></label></div>
            </div>
            <div class="peer"><input type="submit" class="btn btn-primary" value="Login"/></div>
          </div>
        </div>
        <div class="form-group">
          <a class="btn btn-default text-normal text-dark" href="signup.php">Register</a>
        </div>
      </form>
    </div>
  </div>
  <script type="text/javascript" src="static/vendor.js"></script>
  <script type="text/javascript" src="static/bundle.js"></script>
</body>

</html>
