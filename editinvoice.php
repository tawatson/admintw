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

?>
  <!DOCTYPE html>
  <html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <title>Editing: Invoice #</title>
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
      <div class="sidebar">
        <div class="sidebar-inner">
          <div class="sidebar-logo">
            <div class="peers ai-c fxw-nw">
              <div class="peer peer-greed">
                <a class="sidebar-link td-n" href="/">
                  <div class="peers ai-c fxw-nw">
                    <div class="peer">
                      <div class="logo"><img src="static/assets/static/images/logo.png" alt="" style="width:100%; padding: 10px;"></div>
                    </div>
                    <div class="peer peer-greed">
                      <h5 class="lh-1 mB-0 logo-text">Terrence Watson</h5></div>
                  </div>
                </a>
              </div>
              <div class="peer">
                <div class="mobile-toggle sidebar-toggle"><a href="" class="td-n"><i class="ti-arrow-circle-left"></i></a></div>
              </div>
            </div>
          </div>
          <ul class="sidebar-menu scrollable pos-r">
            <li class="nav-item mT-30"><a class="sidebar-link" href="/"><span class="icon-holder"><i class="c-blue-500 ti-home"></i> </span><span class="title">Dashboard</span></a></li>
            <li class="nav-item active"><a class="sidebar-link" href="invoices.php"><span class="icon-holder"><i class="c-red-500 ti-money"></i> </span><span class="title">Invoices</span></a></li>
            <!--<li class="nav-item"><a class="sidebar-link" href="compose.html"><span class="icon-holder"><i class="c-blue-500 ti-share"></i> </span><span class="title">Compose</span></a></li>
          <li class="nav-item"><a class="sidebar-link" href="calendar.html"><span class="icon-holder"><i class="c-deep-orange-500 ti-calendar"></i> </span><span class="title">Calendar</span></a></li>
          <li class="nav-item"><a class="sidebar-link" href="chat.html"><span class="icon-holder"><i class="c-deep-purple-500 ti-comment-alt"></i> </span><span class="title">Chat</span></a></li>
          <li class="nav-item"><a class="sidebar-link" href="charts.html"><span class="icon-holder"><i class="c-indigo-500 ti-bar-chart"></i> </span><span class="title">Charts</span></a></li>
          <li class="nav-item"><a class="sidebar-link" href="forms.html"><span class="icon-holder"><i class="c-light-blue-500 ti-pencil"></i> </span><span class="title">Forms</span></a></li>
          <li class="nav-item dropdown"><a class="sidebar-link" href="ui.html"><span class="icon-holder"><i class="c-pink-500 ti-palette"></i> </span><span class="title">UI Elements</span></a></li>
          <li class="nav-item dropdown"><a class="dropdown-toggle" href="javascript:void(0);"><span class="icon-holder"><i class="c-orange-500 ti-layout-list-thumb"></i> </span><span class="title">Tables</span> <span class="arrow"><i class="ti-angle-right"></i></span></a>
            <ul class="dropdown-menu">
              <li><a class="sidebar-link" href="basic-table.html">Basic Table</a></li>
              <li><a class="sidebar-link" href="datatable.html">Data Table</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown"><a class="dropdown-toggle" href="javascript:void(0);"><span class="icon-holder"><i class="c-purple-500 ti-map"></i> </span><span class="title">Maps</span> <span class="arrow"><i class="ti-angle-right"></i></span></a>
            <ul class="dropdown-menu">
              <li><a href="google-maps.html">Google Map</a></li>
              <li><a href="vector-maps.html">Vector Map</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown"><a class="dropdown-toggle" href="javascript:void(0);"><span class="icon-holder"><i class="c-red-500 ti-files"></i> </span><span class="title">Pages</span> <span class="arrow"><i class="ti-angle-right"></i></span></a>
            <ul class="dropdown-menu">
              <li><a class="sidebar-link" href="blank.html">Blank</a></li>
              <li><a class="sidebar-link" href="404.html">404</a></li>
              <li><a class="sidebar-link" href="500.html">500</a></li>
              <li><a class="sidebar-link" href="signin.html">Sign In</a></li>
              <li><a class="sidebar-link" href="signup.html">Sign Up</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown"><a class="dropdown-toggle" href="javascript:void(0);"><span class="icon-holder"><i class="c-teal-500 ti-view-list-alt"></i> </span><span class="title">Multiple Levels</span> <span class="arrow"><i class="ti-angle-right"></i></span></a>
            <ul class="dropdown-menu">
              <li class="nav-item dropdown"><a href="javascript:void(0);"><span>Menu Item</span></a></li>
              <li class="nav-item dropdown"><a href="javascript:void(0);"><span>Menu Item</span> <span class="arrow"><i class="ti-angle-right"></i></span></a>
                <ul class="dropdown-menu">
                  <li><a href="javascript:void(0);">Menu Item</a></li>
                  <li><a href="javascript:void(0);">Menu Item</a></li>
                </ul>
              </li>
            </ul>
          </li>-->
          </ul>
        </div>
      </div>
      <div class="page-container">
        <div class="header navbar">
          <div class="header-container">
            <ul class="nav-left">
              <li><a id="sidebar-toggle" class="sidebar-toggle" href="javascript:void(0);"><i class="ti-menu"></i></a></li>
            </ul>
            <ul class="nav-right">
              <li class="dropdown">
                <a href="" class="dropdown-toggle no-after peers fxw-nw ai-c lh-1" data-toggle="dropdown">
                  <div class="peer mR-10"><img class="w-2r bdrs-50p" src="https://api.adorable.io/avatars/237/<? echo $_SESSION['username'];?>.png" alt=""></div>
                  <div class="peer"><span class="fsz-sm c-grey-900"><? echo $userInfo['full_name'];?></span></div>
                </a>
                <ul class="dropdown-menu fsz-sm">
                  <li><a href="" class="d-b td-n pY-5 bgcH-grey-100 c-grey-700"><i class="ti-settings mR-10"></i> <span>Setting</span></a></li>
                  <li><a href="" class="d-b td-n pY-5 bgcH-grey-100 c-grey-700"><i class="ti-user mR-10"></i> <span>Profile</span></a></li>
                  <li><a href="email.html" class="d-b td-n pY-5 bgcH-grey-100 c-grey-700"><i class="ti-email mR-10"></i> <span>Messages</span></a></li>
                  <li role="separator" class="divider"></li>
                  <li><a href="logout.php" class="d-b td-n pY-5 bgcH-grey-100 c-grey-700"><i class="ti-power-off mR-10"></i> <span>Logout</span></a></li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
        <main class="main-content bgc-grey-100">
          <div id="mainContent" class="bd bdc-grey-300">
            <div class="container-fluid bgc-white">
              <h3 class="c-grey-900 pT-15 mB-30">Invoice #2018XXX
                <a class="btn btn-success c-white pull-right"><i class="ti-pencil-alt"></i> Save Invoice</a>
              </h3>
              <div class="row mB-30">
                <div class="col-md-3 mL-30">
                  <p><strong>Client Name</strong> (Att: Contact Name)<br> Address Line 1<br> Address Line 2<br> Suburb, ACT, 2600</p>
                </div>
                <div class="col-md-3 ml-auto mR-30 text-right">
                  <p><strong>Issue Date: </strong> XX/XX/XXXX</p>
                  <p><strong>Due Date: </strong> XX/XX/XXXX</p>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="bd bdrs-3 mB-20">
                    <div id="table" class="table-editable">
    <span class="table-add ti-plus"></span>
    <table class="table">
      <tr>
        <th>Item Description</th>
        <th>Item Cost</th>
        <th>Qty</th>
        <th>Item Total</th>
        <th></th>
      </tr>
      <tr>
        <td contenteditable="true">Lorem Ipsum</td>
        <td contenteditable="true" class="itemCost">0.00</td>
        <td contenteditable="true" class="itemQty">0</td>
        <td class="totalItemCost">$0.00</td>
        <td>
          <span class="table-remove ti-close"></span>
          <span class="table-up ti-arrow-up"></span>
          <span class="table-down ti-arrow-down"></span>
        </td>
      </tr>
      <!-- This is our clonable table line -->
      <tr class="d-none">
        <td contenteditable="true">Lorem Ipsum</td>
        <td contenteditable="true" class="itemCost">0.00</td>
        <td contenteditable="true" class="itemQty">0</td>
        <td class="totalItemCost">$0.00</td>
        <td>
          <span class="table-remove ti-close"></span>
          <span class="table-up ti-arrow-up"></span>
          <span class="table-down ti-arrow-down"></span>
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
        <footer class="bdT ta-c p-30 lh-0 fsz-sm c-grey-600"><span>Copyright © 2018 Designed by <a href="https://colorlib.com" target="_blank" title="Colorlib">Colorlib</a>. Backend and related code is by Terrence Watson. All rights reserved.</span></footer>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="static/modal.js"></script>
    <script type="text/javascript" src="static/vendor.js"></script>
    <script type="text/javascript" src="static/bundle.js"></script>
    <script type="text/javascript" src="static/assets/scripts.js"></script>
    <script type="text/javascript" src="static/jquery.formatCurrency-1.4.0.min.js"></script>
    <script>
    $( ".itemCost:not(:hidden), .itemQty" ).keyup(function(event) {
  $(this).siblings(".totalItemCost").text($(this).text() * $(this).siblings(".itemCost:not(:hidden), .itemQty").text()).formatCurrency();
});

    var $TABLE = $('#table');

$('.table-add').click(function () {
var $clone = $TABLE.find('tr.d-none').clone(true).removeClass('d-none');
$TABLE.find('table').append($clone);
$( ".itemCost:not(:hidden), .itemQty" ).keyup(function(event) {
  $(this).siblings(".totalItemCost").text($(this).text() * $(this).siblings(".itemCost:not(:hidden), .itemQty").text()).formatCurrency();
});
});

$('.table-remove').click(function () {
$(this).parents('tr').detach();
});

$('.table-up').click(function () {
var $row = $(this).parents('tr');
if ($row.index() === 1) return; // Don't go above the header
$row.prev().before($row.get(0));
});

$('.table-down').click(function () {
var $row = $(this).parents('tr');
$row.next().after($row.get(0));
});

// A few jQuery helpers for exporting only
jQuery.fn.pop = [].pop;
jQuery.fn.shift = [].shift;

/*$BTN.click(function () {
var $rows = $TABLE.find('tr:not(:hidden)');
var headers = [];
var data = [];

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
    h[header] = $td.eq(i).text();
  });

  data.push(h);
});*/

/*// Output the result
$EXPORT.text(JSON.stringify(data));
});*/
    </script>
  </body>

  </html>
