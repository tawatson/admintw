<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: signin.php?next=%2Finvoices.php");
  exit;
}

require "config.php";

$db->query("SELECT * FROM wa_users WHERE email = :email");
$db->bind(":email", $_SESSION['username']);
$userInfo = $db->single();

?>
  <!DOCTYPE html>
  <html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <title>Clients</title>
    <link rel="stylesheet" href="static/jquery-ui.min.css">
    <link rel="stylesheet" href="static/jquery-ui.structure.css">
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
      <? require 'components/sidebar.php'; ?>
      <div class="page-container">
        <? require "components/nav.php"; ?>
        <main class="main-content bgc-grey-100">
          <div id="mainContent">
            <div class="container-fluid">
              <h3 class="c-grey-900 mT-10 mB-30">Clients
                <a class="btn btn-primary c-white pull-right" data-action="newClient" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="ti-plus"></i> New Client</a>
              </h3>
              <div class="row">

                        <?
                          // Load Invoices
                          $db->query("SELECT * FROM wa_clients");
                          $clients = $db->resultSet();
                          if(empty($clients)){
                            echo '<h3>Nothing to show here...</h3>';
                          } else {
                            foreach ($clients as $client) {
                        ?>
                    <div class="col-md-12">
                      <div class="p-20 mB-20">
                        <div class="card">
                          <div class="card-body">
                            <a class="mB-0 h3" href="client.php?id=<? echo $client['id'];?>"><? echo $client['name'];?></a>
                          </div>
                        </div>
                      </div>
                    </div>
                            <?
                          }
                        }?>


              </div>
            </div>
            <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="newClient" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title"><i class="ti-money"></i> New Client</h5>
                  </div>
                  <div class="modal-body">
                    <form id="clientForm">
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary createClient">Create Client</button>
                    <input type="reset" form="invoiceForm" class="btn btn-secondary" data-dismiss="modal">
                  </div>
                </div>
              </div>
            </div>

          </div>
        </main>
      <? require "components/footer.php";?>
    <script type="text/javascript" src="static/jquery-ui.min.js"></script>
    <script>
        $(".createClient").click(function (){
          $.ajax({
              url: "ajax.php",
              method: "post",
              data: $("#invoiceForm").serialize()+"&action=createClient",
              success: function( data ) {
                  window.location = "/client.php?id="+data;
              }
          });

        });
        });
    </script>
  </body>

  </html>
