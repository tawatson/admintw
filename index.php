<?php
// Initialize the session
session_start();

// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: signin.php");
  exit;
}

require "config.php";

$db->query("SELECT * FROM wa_users WHERE email = :email");
$db->bind(":email", $_SESSION['username']);
$userInfo = $db->single();

$db->query("SELECT * FROM wa_repos");
$repos = $db->resultSet();

$ganalytics->setAccountId('ga:150420384');
//GANALYTICS SITE VISITS (TO DO: MAKE THIS AJAX TO EASE LOAD TIME)
$params = array(
	'metrics' => 'ga:visits',
	'dimensions' => 'ga:date'
);
$visits = $ganalytics->query($params);
$numVisits = $visits['totalsForAllResults']['ga:visits'];

$prevParams = array(
  'start-date' => date('Y-m-d', strtotime('-2 months')),
  'end-date' => date('Y-m-d', strtotime('-1 month')),
  'metrics' => 'ga:visits',
  'dimensions' => 'ga:date'
);
$analyticsData = $ganalytics->query($prevParams);
$prevVisits = $analyticsData['totalsForAllResults']['ga:visits'];
$diffVisits = $numVisits - $prevVisits;

$params['metrics'] = "ga:pageviews";
$pageviews = $ganalytics->query($params);
$numPageViews = $pageviews['totalsForAllResults']['ga:pageviews'];

$prevParams['metrics'] = "ga:pageviews";
$prevPageViews = $ganalytics->query($prevParams);
$diffPageViews = $numPageViews - $prevPageViews['totalsForAllResults']['ga:pageviews'];

$params['metrics'] = "ga:uniquePageviews";
$uniqueData = $ganalytics->query($params);
$uniqueViews = $uniqueData['totalsForAllResults']['ga:uniquePageviews'];

$prevParams['metrics'] = "ga:uniquePageviews";
$prevUniqueViews = $ganalytics->query($prevParams);
$diffUniqueViews = $uniqueViews - $prevUniqueViews['totalsForAllResults']['ga:uniquePageviews'];

$params['metrics'] = "ga:bounceRate";
$bounceData = $ganalytics->query($params);
$bounceRate = $bounceData['totalsForAllResults']['ga:bounceRate'];

$prevParams['metrics'] = "ga:bounceRate";
$prevBounceData = $ganalytics->query($prevParams);
$diffBounceRate = $bounceRate - $prevBounceData['totalsForAllResults']['ga:bounceRate'];


// COUNTRY DATA
$countryParams = array(
  'metrics' => 'ga:pageviews',
  'dimensions' => 'ga:country',
  'sort' => '-ga:pageviews',
  'max-results' => 30,
);
$countryvisits = $ganalytics->query($countryParams);
$jvmData = Array();

foreach ($countryvisits['rows'] as $row) {
  $jvmData[$countryCodes[$row[0]]] = $row[1];
}

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
  <title>Dashboard</title>
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
        <div id="mainContent">
          <div class="row gap-20 masonry pos-r">
            <div class="masonry-sizer col-md-6"></div>
            <div class="masonry-item w-100">
              <div class="row gap-20">
                <div class="col-md-3">
                  <div class="layers bd bgc-white p-20">
                    <div class="layer w-100 mB-10">
                      <h6 class="lh-1">Total Visits</h6></div>
                    <div class="layer w-100">
                      <div class="peers ai-sb fxw-nw">
                        <div class="peer peer-greed"><h4><? echo $prevVisits; ?></h4></div>
                        <div class="peer"><span class="d-ib lh-0 va-m fw-600 bdrs-10em pX-15 pY-15 bgc-green-50 c-green-500"><? echo $diffVisits;?> change</span></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="layers bd bgc-white p-20">
                    <div class="layer w-100 mB-10">
                      <h6 class="lh-1">Total Page Views</h6></div>
                    <div class="layer w-100">
                      <div class="peers ai-sb fxw-nw">
                        <div class="peer peer-greed"><h4><? echo $numPageViews;?></h4></div>
                        <div class="peer"><span class="d-ib lh-0 va-m fw-600 bdrs-10em pX-15 pY-15 bgc-red-50 c-red-500"><? echo $diffPageViews;?> change</span></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="layers bd bgc-white p-20">
                    <div class="layer w-100 mB-10">
                      <h6 class="lh-1">Unique Visitor</h6></div>
                    <div class="layer w-100">
                      <div class="peers ai-sb fxw-nw">
                        <div class="peer peer-greed"><h4><? echo $uniqueViews;?></h4></div>
                        <div class="peer"><span class="d-ib lh-0 va-m fw-600 bdrs-10em pX-15 pY-15 bgc-purple-50 c-purple-500"><? echo $diffUniqueViews;?> change</span></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="layers bd bgc-white p-20">
                    <div class="layer w-100 mB-10">
                      <h6 class="lh-1">Bounce Rate</h6></div>
                    <div class="layer w-100">
                      <div class="peers ai-sb fxw-nw">
                        <div class="peer peer-greed"><h4><? echo round($bounceRate);?>%</h4></div>
                        <div class="peer"><span class="d-ib lh-0 va-m fw-600 bdrs-10em pX-15 pY-15 bgc-blue-50 c-blue-500"><? echo round($diffBounceRate);?>% change</span></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="masonry-item col-12">
              <div class="bd bgc-white">
                <div class="peers fxw-nw@lg+ ai-s">
                  <div class="peer peer-greed w-70p@lg+ w-100@lg- p-20">
                    <div class="layers">
                      <div class="layer w-100 mB-10">
                        <h6 class="lh-1">Site Visits</h6></div>
                      <div class="layer w-100">
                        <div id="world-map-marker" style="height:440px;width:100%;"></div>
                      </div>
                    </div>
                  </div>
                  <div class="peer bdL p-20 w-30p@lg+ w-100p@lg-">
                    <div class="layers">
                      <div class="layer w-100">
                        <div class="layers">
                          <div class="layer w-100">
                            <h5 class="mB-5"><? echo $jvmData['AU'];?></h5><small class="fw-600 c-grey-700">Visitors From Australia</small>
                          </div>
                          <div class="layer w-100 mT-15">
                            <h5 class="mB-5"><? echo $jvmData['US'];?></h5><small class="fw-600 c-grey-700">Visitors From United States</small>
                          </div>
                          <div class="layer w-100 mT-15">
                            <h5 class="mB-5"><? echo $jvmData['GB'];?></h5><small class="fw-600 c-grey-700">Visitors From United Kingdom</small>
                          </div>
                          <div class="layer w-100 mT-15">
                            <h5 class="mB-5"><? echo $jvmData['IN'];?></h5><small class="fw-600 c-grey-700">Visitors From India</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="masonry-item col-md-12">
              <div class="bd bgc-white">
                <div class="layers">
                  <div class="layer w-100 pX-20 pT-20">
                    <h6 class="lh-1">Software Status <a class="pull-right refreshRepos"><i class="fa fa-refresh"></i></a></h6></div>
                  <div class="layer p-20 w-100">
                    <table class="table table-hover">
                      <tr>
                        <th>Repo</th>
                        <th>Last Pull</th>
                        <th>Status</th>
                      </tr>
                    <? foreach ($repos as $repo) {
                      $repoClass = new Tawatson_gitHook($db,$repo['local_dir'],$repo['repo_name']);
                      ?>

                      <tr>
                            <td><? echo $repo['tidy_name'];?></td>
                            <td><? echo $timeAgo->inWords(date("M jS, Y g:ia", strtotime($repo['last_pull']. " + 17 hours")));?></td>
                            <? if($repoClass->isUpToDate()){?>
                            <td class="table-success" id="repo-<?echo $repo['id'];?>">Up to Date</td>
                            <?} else {?>
                            <td class="table-warning" id="repo-<?echo $repo['id'];?>"><h4>Out of Date! <a href="javascript:void(0);" data-repo="<? echo $repo['id'];?>"  class="repoUpdate btn btn-xs btn-success pull-right">Update Now <i class="fa fa-download"></i></a></h4></td>
                            <?}?>
                        </tr>

                    <? } ?>
                  </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="masonry-item col-md-6">
              <div class="bd bgc-white p-20">
                <div class="layers">
                  <div class="layer w-100 mB-10">
                    <h6 class="lh-1">Todo List</h6></div>
                  <div class="layer w-100">
                    <ul class="list-task list-group" data-role="tasklist">
                      <li class="list-group-item bdw-0" data-role="task">
                        <div class="checkbox checkbox-circle checkbox-info peers ai-c"><input type="checkbox" id="inputCall1" name="inputCheckboxesCall" class="peer"> <label for="inputCall1" class="peers peer-greed js-sb ai-c"><span class="peer peer-greed">Call John for Dinner</span></label></div>
                      </li>
                      <li class="list-group-item bdw-0" data-role="task">
                        <div class="checkbox checkbox-circle checkbox-info peers ai-c"><input type="checkbox" id="inputCall2" name="inputCheckboxesCall" class="peer"> <label for="inputCall2" class="peers peer-greed js-sb ai-c"><span class="peer peer-greed">Book Boss Flight</span> <span class="peer"><span class="badge badge-pill fl-r badge-success lh-0 p-10">2 Days</span></span></label></div>
                      </li>
                      <li class="list-group-item bdw-0" data-role="task">
                        <div class="checkbox checkbox-circle checkbox-info peers ai-c"><input type="checkbox" id="inputCall3" name="inputCheckboxesCall" class="peer"> <label for="inputCall3" class="peers peer-greed js-sb ai-c"><span class="peer peer-greed">Hit the Gym</span> <span class="peer"><span class="badge badge-pill fl-r badge-danger lh-0 p-10">3 Minutes</span></span></label></div>
                      </li>
                      <li class="list-group-item bdw-0" data-role="task">
                        <div class="checkbox checkbox-circle checkbox-info peers ai-c"><input type="checkbox" id="inputCall4" name="inputCheckboxesCall" class="peer"> <label for="inputCall4" class="peers peer-greed js-sb ai-c"><span class="peer peer-greed">Give Purchase Report</span> <span class="peer"><span class="badge badge-pill fl-r badge-warning lh-0 p-10">not important</span></span></label></div>
                      </li>
                      <li class="list-group-item bdw-0" data-role="task">
                        <div class="checkbox checkbox-circle checkbox-info peers ai-c"><input type="checkbox" id="inputCall5" name="inputCheckboxesCall" class="peer"> <label for="inputCall5" class="peers peer-greed js-sb ai-c"><span class="peer peer-greed">Watch Game of Thrones Episode</span> <span class="peer"><span class="badge badge-pill fl-r badge-info lh-0 p-10">Tomorrow</span></span></label></div>
                      </li>
                      <li class="list-group-item bdw-0" data-role="task">
                        <div class="checkbox checkbox-circle checkbox-info peers ai-c"><input type="checkbox" id="inputCall6" name="inputCheckboxesCall" class="peer"> <label for="inputCall6" class="peers peer-greed js-sb ai-c"><span class="peer peer-greed">Give Purchase report</span> <span class="peer"><span class="badge badge-pill fl-r badge-success lh-0 p-10">Done</span></span></label></div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <div class="masonry-item col-md-6">
              <div class="bd bgc-white">
                <div class="layers">
                  <div class="layer w-100 p-20">
                    <h6 class="lh-1">Sales Report</h6></div>
                  <div class="layer w-100">
                    <div class="bgc-light-blue-500 c-white p-20">
                      <div class="peers ai-c jc-sb gap-40">
                        <div class="peer peer-greed">
                          <h5>November 2017</h5>
                          <p class="mB-0">Sales Report</p>
                        </div>
                        <div class="peer">
                          <h3 class="text-right">$6,000</h3></div>
                      </div>
                    </div>
                    <div class="table-responsive p-20">
                      <table class="table">
                        <thead>
                          <tr>
                            <th class="bdwT-0">Name</th>
                            <th class="bdwT-0">Status</th>
                            <th class="bdwT-0">Date</th>
                            <th class="bdwT-0">Price</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td class="fw-600">Item #1 Name</td>
                            <td><span class="badge bgc-red-50 c-red-700 p-10 lh-0 tt-c badge-pill">Unavailable</span></td>
                            <td>Nov 18</td>
                            <td><span class="text-success">$12</span></td>
                          </tr>
                          <tr>
                            <td class="fw-600">Item #2 Name</td>
                            <td><span class="badge bgc-deep-purple-50 c-deep-purple-700 p-10 lh-0 tt-c badge-pill">New</span></td>
                            <td>Nov 19</td>
                            <td><span class="text-info">$34</span></td>
                          </tr>
                          <tr>
                            <td class="fw-600">Item #3 Name</td>
                            <td><span class="badge bgc-pink-50 c-pink-700 p-10 lh-0 tt-c badge-pill">New</span></td>
                            <td>Nov 20</td>
                            <td><span class="text-danger">-$45</span></td>
                          </tr>
                          <tr>
                            <td class="fw-600">Item #4 Name</td>
                            <td><span class="badge bgc-green-50 c-green-700 p-10 lh-0 tt-c badge-pill">Unavailable</span></td>
                            <td>Nov 21</td>
                            <td><span class="text-success">$65</span></td>
                          </tr>
                          <tr>
                            <td class="fw-600">Item #5 Name</td>
                            <td><span class="badge bgc-red-50 c-red-700 p-10 lh-0 tt-c badge-pill">Used</span></td>
                            <td>Nov 22</td>
                            <td><span class="text-success">$78</span></td>
                          </tr>
                          <tr>
                            <td class="fw-600">Item #6 Name</td>
                            <td><span class="badge bgc-orange-50 c-orange-700 p-10 lh-0 tt-c badge-pill">Used</span></td>
                            <td>Nov 23</td>
                            <td><span class="text-danger">-$88</span></td>
                          </tr>
                          <tr>
                            <td class="fw-600">Item #7 Name</td>
                            <td><span class="badge bgc-yellow-50 c-yellow-700 p-10 lh-0 tt-c badge-pill">Old</span></td>
                            <td>Nov 22</td>
                            <td><span class="text-success">$56</span></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="ta-c bdT w-100 p-20"><a href="#">Check all the sales</a></div>
              </div>
            </div>
          </div>
        </div>
      </main>
      <? require "components/footer.php";?>
  <script>
    var countryData = <? echo json_encode($jvmData);?>;
      $(function(){
      $('#world-map-marker').vectorMap({
        map: 'world_mill',
        regionStyle: { initial: { fill: '#EEEEEE' } },
        series: {
          regions: [{
            values: countryData,
            scale: ['#e5dada', '#d80606'],
            normalizeFunction: 'polynomial'
          }]
        },
        backgroundColor: '#FFFFFF',
        onRegionTipShow: function(event, label, code){
          if (countryData[code] != null) {
             $(label).text($(label).text() + ": " + countryData[code]);
          } else {
            $(label).text($(label).text() + ": 0");
          }
        }
      });
    });
  </script>

</body>

</html>
