<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: signin.php?next=".urlencode($_SERVER['REQUEST_URI']));
  exit;
}

require "config.php";

$db->query("SELECT * FROM wa_users WHERE email = :email");
$db->bind(":email", $_SESSION['username']);
$userInfo = $db->single();

$clientId = $_GET['id'];

$db->query("SELECT * FROM wa_clients WHERE id = :id");
$db->bind(":id", $clientId);
$clientInfo = $db->single();
?>
  <!DOCTYPE html>
  <html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <title>Client:
      <? echo $clientInfo['name'];?>
    </title>
    <? require "components/webapp.php";?>
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
    <div>
      <? require "components/sidebar.php";?>
        <div class="page-container">
          <? require "components/nav.php"; ?>
            <main class="main-content bgc-grey-100">
              <div id="mainContent" class="bd bdc-grey-300">
                <div class="container-fluid bgc-white">
                  <h3 class="c-grey-900 pT-15 mB-30"><? echo $clientInfo['name'];?>
                <div class="btn-group pull-right">
                  <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Actions</button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item stay" href="editclient.php?id=<? echo $clientId;?>" target="_self"><i class="ti-pencil-alt mR-10"></i> Edit Client</a>
                    <a class="dropdown-item stay" href="#"><i class="ti-trash mR-10"></i> Delete Client</a>
                  </div>
                </div>
              </h3>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group row"><label class="col-sm-3 col-form-label">Contact Name</label>
                        <div class="col-sm-9"><input type="text" class="form-control" placeholder="Contact Name" readonly value="<? echo (empty($clientInfo['contact_name'])? "N/A" : $clientInfo['contact_name']); ?>"></div>
                      </div>
                      <div class="form-group row"><label class="col-sm-3 col-form-label">Address Line 1</label>
                        <div class="col-sm-9"><input type="text" class="form-control" placeholder="Address 1" readonly value="<? echo  $clientInfo['address1']; ?>"></div>
                      </div>
                      <div class="form-group row"><label class="col-sm-3 col-form-label">Address Line 2</label>
                        <div class="col-sm-9"><input type="text" class="form-control" placeholder="Address 2" readonly value="<? echo (empty($clientInfo['address2'])? "N/A" : $clientInfo['address2']); ?>"></div>
                      </div>
                      <div class="form-group row"><label class="col-sm-3 col-form-label">Suburb</label>
                        <div class="col-sm-9"><input type="text" class="form-control" placeholder="Suburb" readonly value="<? echo $clientInfo['suburb']; ?>"></div>
                      </div>
                      <div class="form-group row"><label class="col-sm-3 col-form-label">State/Territory</label>
                        <div class="col-sm-9"><input type="text" class="form-control" placeholder="State" readonly value="<? echo $clientInfo['state']; ?>"></div>
                      </div>
                      <div class="form-group row"><label class="col-sm-3 col-form-label">Postcode</label>
                        <div class="col-sm-9"><input type="text" class="form-control" placeholder="Postcode" readonly value="<? echo $clientInfo['postcode']; ?>"></div>
                      </div>
                      <div class="form-group row"><label class="col-sm-3 col-form-label">Email</label>
                        <div class="col-sm-9"><input type="text" class="form-control" placeholder="Email" readonly value="<? echo $clientInfo['contact_email']; ?>"></div>
                      </div>
                      <div class="form-group row"><label class="col-sm-3 col-form-label">Phone</label>
                        <div class="col-sm-9"><input type="text" class="form-control" placeholder="Phone" readonly value="<? echo $clientInfo['contact_number']; ?>"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </main>
            <? require "components/footer.php";?>
  </body>

  </html>
