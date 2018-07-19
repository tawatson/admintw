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


$invoiceId = $_GET['id'];

// Get invoice info
$db->query("SELECT * FROM wa_invoices WHERE id = :id");
$db->bind(":id", $invoiceId);
$invoiceInfo = $db->single();

// Get Client info
$db->query("SELECT * FROM wa_clients WHERE id = :id");
$db->bind(":id", $invoiceInfo['client_id']);
$clientInfo = $db->single();

// Get Current Invoice items
$db->query("SELECT * FROM wa_invoice_items WHERE invoice_id = :id");
$db->bind(":id", $invoiceId);
$items = $db->resultSet();

$cost = array();

foreach ($items as $item) {
  $itemCost = $item['cost'] * $item['qty'];
  $cost[] = $itemCost;
}
?>
  <!DOCTYPE html>
  <html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <title>Invoice #<? echo $invoiceId;?></title>
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

  <body class="app is-collapsed">
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
              <h3 class="c-grey-900 pT-15 mB-30">Invoice #<? echo $invoiceId;?>
                <div class="btn-group pull-right">
                  <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Actions</button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item stay" href="editinvoice.php?id=<? echo $invoiceId;?>"><i class="ti-pencil-alt mR-10"></i> Edit Invoice</a>
                    <a class="dropdown-item stay" href="#"><i class="ti-check mR-10"></i> Mark Invoice as Paid</a>
                    <a class="dropdown-item stay" href="#"><i class="ti-trash mR-10"></i> Delete Invoice</a>
                  </div>
                </div>
              </h3>
              <div class="row mB-30">
                <div class="col-md-3 mL-30">
                  <p><strong><? echo $clientInfo['name'];?></strong>
                    <? if(!empty($clientInfo['contact_name'])){ echo"<br/>(Att: ".$clientInfo['contact_name'].")";}?><br>
                      <? echo $clientInfo['address1'];?><br>
                        <? if(!empty($clientInfo['address2'])){ echo $clientInfo['address2']."<br/>";} echo $clientInfo['suburb'].", ".$clientInfo['state'].", ".$clientInfo['postcode'];?></p>
                </div>
                <div class="col-md-3 ml-auto mR-30 text-right">
                  <p><strong>Issue Date: </strong>
                    <? echo date('M jS, Y', strtotime($invoiceInfo['issue_date']));?>
                  </p>
                  <p><strong>Due Date: </strong>
                    <? echo date('M jS, Y', strtotime($invoiceInfo['due_date']));?>
                  </p>
                  <h3>Total: <span class="d-ib <? echo ($invoiceInfo['paid'] == 1 ? "bgc-green-50 bdc-green-500 c-green-900" : "bgc-red-50 bdc-red-500 c-red-900")?> p-10 bd  bdrs-10em">$<? echo number_format(array_sum($cost), 2, '.', ',');?></span></h3>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="bd bdrs-3 mB-20">
                    <table class="table">
                      <tr>
                        <th>Item Description</th>
                        <th>Item Cost</th>
                        <th>Qty</th>
                        <th>Item Total</th>
                      </tr>
                      <? if(!empty($items)){
                        foreach ($items as $item) {?>
                        <tr>
                          <td>
                            <? echo $item['description'];?>
                          </td>
                          <td>
                            <? echo "$".number_format($item['cost'], 2, '.', ',');?>
                          </td>
                          <td>
                            <? echo $item['qty'];?>
                          </td>
                          <td>
                            <? echo "$".number_format($item['cost'] * $item['qty'], 2, '.', ',');?>
                          </td>
                        </tr>
                        <?  }
                      } else {

                          echo "<tr><td colspan='4'><h3>No items to display</h3></td></tr>";
                      }?>

                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>
<? require "components/footer.php";?>
  </body>

  </html>
