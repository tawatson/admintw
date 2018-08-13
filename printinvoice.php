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
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #<? echo $invoiceId;?></title>

<style>
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        font-size: 16px;
        line-height: 24px;
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #555;
    }

    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }

    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }

    .invoice-box table tr td:nth-child(2) {
        text-align: right;
    }

    .invoice-box table tr.top table td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.top table td.title {
        font-size: 45px;
        line-height: 45px;
        color: #333;
    }

    .invoice-box table tr.information table td {
        padding-bottom: 40px;
    }

    .invoice-box table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }

    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }

    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
    }

    .invoice-box table tr.item.last td {
        border-bottom: none;
    }

    .invoice-box table tr.total td:nth-child(2) {
        border-top: 2px solid #eee;
        font-weight: bold;
    }

    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }

        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }

    /** RTL **/
    .rtl {
        direction: rtl;
        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    }

    .rtl table {
        text-align: right;
    }

    .rtl table tr td:nth-child(2) {
        text-align: left;
    }
</style>

</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="/static/assets/static/images/logo.png" style="width:100%; max-width:300px;">
                            </td>

                            <td>
                                Invoice #<? echo $invoiceId; ?><br>
                                Issued: <? echo date('M jS, Y', strtotime($invoiceInfo['issue_date']));?><br>
                                Due: <? echo date('M jS, Y', strtotime($invoiceInfo['due_date']));?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                Terrence Arthur Watson<br>
                                <strong>ABN:</strong> 61 152 182 256<br>
                                <a href="mailto:me@terrenceawatson.com">me@terrenceawatson.com</a>
                            </td>

                            <td>
                                <? echo $clientInfo['name'];?><br>
                                <? if(!empty($clientInfo['contact_name'])){ echo "(Att: ".$clientInfo['contact_name'].") <br/>";}?>
                                <? echo $clientInfo['contact_email'];?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
              <td>Item Description</td>
              <td>Item Cost</td>
              <td>Qty</td>
              <td>Item Total</td>
            </tr>

            <? if(!empty($items)){
              foreach ($items as $item) {?>
              <tr class="item">
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

                echo "<tr class='item'><td colspan='4'><h3>No items to display</h3></td></tr>";
            }?>

            <tr class="total">
                <td></td>

                <td>
                   Total: $385.00
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
