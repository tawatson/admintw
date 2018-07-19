<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: signin.php?next=".urlencode($_SERVER['REQUEST_URI']));
  exit;
}

if(!isset($_GET['id'])){
  header("location: /invoices.php");
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
?>
  <!DOCTYPE html>
  <html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <title>Editing: Invoice #
      <? echo $invoiceId;?>
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
    <? require 'components/sidebar.php';?>
      <div class="page-container">
        <? require "components/nav.php"; ?>
        <main class="main-content bgc-grey-100">
          <div id="mainContent" class="bd bdc-grey-300">
            <div class="container-fluid bgc-white">
              <h3 class="c-grey-900 pT-15 mB-30">Invoice #<? echo $invoiceId;?>
                <a class="btn btn-success c-white pull-right saveInvoiceItems"><i class="ti-pencil-alt"></i> Save Invoice</a>
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
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="bd bdrs-3 mB-20">
                    <div id="table" class="table-editable">
                      <span class="table-add ti-plus" data-toggle="tooltip" data-placement="top" title="Add Item"></span>
                      <table class="table">
                        <tr>
                          <th>Item ID</th>
                          <th>Item Description</th>
                          <th>Item Cost</th>
                          <th>Qty</th>
                          <th>Item Total</th>
                          <th></th>
                        </tr>
                        <? if(!empty($items)){
        foreach ($items as $item) {?>
                          <tr>
                            <td>
                              <? echo $item['id'];?>
                            </td>
                            <td contenteditable="true">
                              <? echo $item['description'];?>
                            </td>
                            <td contenteditable="true" class="itemCost">
                              <? echo $item['cost'];?>
                            </td>
                            <td contenteditable="true" class="itemQty">
                              <? echo $item['qty'];?>
                            </td>
                            <td class="totalItemCost">
                              <? echo "$".$item['cost'] * $item['qty'];?>
                            </td>
                            <td>
                              <span class="table-remove ti-close" data-toggle="tooltip" data-placement="top" title="Remove Item"></span>
                              <span class="table-up ti-arrow-up" data-toggle="tooltip" data-placement="top" title="Shift Item Up"></span>
                              <span class="table-down ti-arrow-down" data-toggle="tooltip" data-placement="top" title="Shift Item Down"></span>
                            </td>
                          </tr>
                          <?  }
                              }?>
                              <!-- This is our clonable table line -->
                              <tr class="d-none">
                                <td>0</td>
                                <td contenteditable="true">Lorem Ipsum</td>
                                <td contenteditable="true" class="itemCost">0.00</td>
                                <td contenteditable="true" class="itemQty">0</td>
                                <td class="totalItemCost">$0.00</td>
                                <td>
                                  <span class="table-remove ti-close" data-toggle="tooltip" data-placement="top" title="Remove Item"></span>
                                  <span class="table-up ti-arrow-up" data-toggle="tooltip" data-placement="top" title="Shift Item Up"></span>
                                  <span class="table-down ti-arrow-down" data-toggle="tooltip" data-placement="top" title="Shift Item Down"></span>
                                </td>
                              </tr>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </main>
    <? require 'components/footer.php';?>
    <script type="text/javascript" src="static/jquery.formatCurrency-1.4.0.min.js"></script>
    <script>
      $(".itemCost:not(:hidden), .itemQty").keyup(function(event) {
        $(this).siblings(".totalItemCost").text($(this).text() * $(this).siblings(".itemCost:not(:hidden), .itemQty").text()).formatCurrency();
      });

      var $TABLE = $('#table');

      $('.table-add').click(function() {
        var $clone = $TABLE.find('tr.d-none').clone(true).removeClass('d-none');
        $TABLE.find('table').append($clone);
        $(".itemCost:not(:hidden), .itemQty").keyup(function(event) {
          $(this).siblings(".totalItemCost").text($(this).text() * $(this).siblings(".itemCost:not(:hidden), .itemQty").text()).formatCurrency();
        });
      });

      $('.table-remove').click(function() {
        $(this).parents('tr').detach();
      });

      $('.table-up').click(function() {
        var $row = $(this).parents('tr');
        if ($row.index() === 1) return; // Don't go above the header
        $row.prev().before($row.get(0));
      });

      $('.table-down').click(function() {
        $(this).addClass("disabled").text("Saving...");
        var $row = $(this).parents('tr');
        $row.next().after($row.get(0));
      });

      // A few jQuery helpers for exporting only
      jQuery.fn.pop = [].pop;
      jQuery.fn.shift = [].shift;

      $(".saveInvoiceItems").click(function () {
        $(this).addClass("disabled").text('Saving...');
      var $rows = $TABLE.find('tr:not(:hidden)');
      var headers = [];
      var $data = [];

      // Get the headers (add special header logic here)
      $($rows.shift()).find('th:not(:empty)').each(function () {
        headers.push($(this).text().toLowerCase());
      });

      // Turn all existing rows into a loopable array
      $rows.each(function () {
        var $td = $(this).find('td');
        var h = {};

        // Use the headers from earlier to name our hash keys
        headers.forEach(function (header, i) {
          h[header] = $td.eq(i).text().trim();
        });

        $data.push(h);
      });

      $.post({
        url: "ajax.php",
        data: {
          data: JSON.stringify($data),
          action: "saveInvoiceItems",
		  invoice_id: "<? echo $invoiceId;?>"
        },
        success: function (response) {
          $(".saveInvoiceItems").text('Saved');
          window.location.href = "invoice.php?id=<? echo $invoiceId;?>";
        },
        error: function(xhr, status, error) {
          var err = eval("(" + xhr.responseText + ")");
          alert(err.Message);
        }
      });

      });
    </script>
  </body>

  </html>
