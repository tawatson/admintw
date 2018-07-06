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
      <li class="nav-item mT-30 <? if(strpos($_SERVER['REQUEST_URI'], '/') !== false){ echo 'active';}?>"><a class="sidebar-link" href="/"><span class="icon-holder"><i class="c-blue-500 ti-home"></i> </span><span class="title">Dashboard</span></a></li>
        <? if(strpos($_SERVER['REQUEST_URI'], '/invoice.php') !== false || strpos($_SERVER['REQUEST_URI'], '/editinvoice.php') !== false){ ?>
          <li class="nav-item dropdown open"><a class="sidebar-link" href="invoices.php"><span class="icon-holder"><i class="c-red-500 ti-money"></i> </span><span class="title">Invoices</span> <span class="arrow"><i class="ti-angle-right"></i></span></a>
        <?  if(strpos($_SERVER['REQUEST_URI'], '/invoice.php') !== false){?>
            <ul class="dropdown-menu"  style="display: block;">
              <li><a class="sidebar-link " href="javascript:void(0);">View Invoice</a></li>
            </ul>
        <?  } else { ?>
          <ul class="dropdown-menu" style="display: block;">
            <li><a class="sidebar-link " href="javascript:void(0);">Edit Invoice</a></li>
          </ul>
        <? }
      } else {?>
        <li class="nav-item <? if(strpos($_SERVER['REQUEST_URI'], '/invoices.php') !== false){ echo 'active';}?>"><a class="sidebar-link" href="invoices.php"><span class="icon-holder"><i class="c-red-500 ti-money"></i> </span><span class="title">Invoices</span></a>
    <?  }?>


      </li>

    </ul>
  </div>
</div>
