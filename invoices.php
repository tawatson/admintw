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
    <title>Invoices</title>
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
              <h3 class="c-grey-900 mT-10 mB-30">Invoices
                <a class="btn btn-primary c-white pull-right" data-action="newInvoice" data-toggle="modal" data-target=".bd-example-modal-lg"><i class="ti-plus"></i> New Invoice</a>
              </h3>
              <div class="row">
                <div class="col-md-12">
                  <div class="bgc-white bd bdrs-3 p-20 mB-20">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Client</th>
                          <th scope="col">Invoice Amount</th>
                          <th scope="col">Issue Date</th>
                          <th scope="col">Due Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?
                          // Load Invoices
                          $db->query("SELECT * FROM wa_invoices");
                          $invoices = $db->resultSet();
                          if(empty($invoices)){
                            echo '<td colspan="5" style="text-align:center"><h3>No invoices to display...</h3></td>';
                          } else {
                            foreach ($invoices as $invoice) {
                              // Load Client Name
                              $db->query('SELECT name FROM wa_clients WHERE id = :id');
                              $db->bind(":id", $invoice['client_id']);
                              $client = $db->single();

                              // Load Invoice Cost
                              $db->query("SELECT cost, qty FROM wa_invoice_items WHERE invoice_id = :id");
                              $db->bind(":id", $invoice['id']);
                              $invoice['items'] = $db->resultSet();

                              $cost = array();

                              foreach ($invoice['items'] as $item) {
                                $itemCost = $item['cost'] * $item['qty'];
                                $cost[] = $itemCost;
                              }
                        ?>
                        <tr role="button" style="cursor: pointer;" onclick="window.location = '/invoice.php?id=<? echo $invoice['id'];?>'">
                          <th scope="row"><? echo $invoice['id'];?></th>
                          <td><? echo $client['name'];?></td>
                          <td><? echo "$".number_format(array_sum($cost), 2, '.', ',');?></td>
                          <td><? echo date('M jS, Y', strtotime($invoice['issue_date']));?></td>
                          <td><? echo date('M jS, Y', strtotime($invoice['due_date']));?></td>
                        </tr>
                            <?
                          }
                        }?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="newInvoice" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title"><i class="ti-money"></i> New Invoice</h5>
                  </div>
                  <div class="modal-body">
                    <form id="invoiceForm">
                      <input type="hidden" name="action" value="createInvoice"/>
                      <div class="form-group">
                        <label for="client">Client</label>
                        <input type="text" class="form-control" id="client" name="client" placeholder="Start typing to search...">
                      </div>
                      <div class="form-row">
                        <div class="form-group col-md-6"><label class="fw-500">Issue Date</label>
                          <div class="timepicker-input input-icon form-group">
                            <div class="input-group">
                              <div class="input-group-addon bgc-white bd bdwR-0"><i class="ti-calendar"></i></div><input type="text" class="form-control bdc-grey-200 date" data-provide="datepicker" name="issueDate"></div>
                          </div>
                        </div>
                        <div class="form-group col-md-6"><label class="fw-500">Due Date</label>
                          <div class="timepicker-input input-icon form-group">
                            <div class="input-group">
                              <div class="input-group-addon bgc-white bd bdwR-0"><i class="ti-calendar"></i></div><input type="text" class="form-control bdc-grey-200 date"  data-provide="datepicker" name="dueDate"></div>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-primary createInvoice">Create Invoice</button>
                    <button type="reset" form="invoiceForm" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </main>
      <? require "components/footer.php";?>
    <script type="text/javascript" src="static/jquery-ui.min.js"></script>
    <script>
      // AUTOCOMPLETE FROM DATABASE
      $(function() {
        $( "#client" ).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    url: "ajax.php",
                    dataType: "json",
                    data: {
                        q: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
        });

        $(".createInvoice").click(function (){
          $.ajax({
              url: "ajax.php",
              method: "post",
              data: $("#invoiceForm").serialize()+"&action=createInvoice",
              success: function( data ) {
                  window.location = "/editinvoice.php?id="+data;
              }
          });

        });
        });
    </script>
  </body>

  </html>
