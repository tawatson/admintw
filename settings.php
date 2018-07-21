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

$db->query("SELECT * FROM wa_repos");
$repos = $db->resultSet();

?>
  <!DOCTYPE html>
  <html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <title>Settings</title>
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
      <? require "components/sidebar.php"; ?>
      <div class="page-container">
        <? require "components/nav.php"; ?>
        <main class="main-content bgc-grey-100">
          <div id="mainContent">
              <div class="col-md-6 bd bdc-grey-300 bgc-white">
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
                              <td class="table-warning" id="repo-<?echo $repo['id'];?>"><h4>Out of Date! <a data-repo="<? echo $repo['id'];?>"  class="repoUpdate btn btn-xs btn-success pull-right">Update Now <i class="fa fa-download"></i></a></h4></td>
                              <?}?>
                          </tr>

                      <? } ?>
                    </table>
                    </div>
                  </div>
            </div>
          </div>
        </main>
        <? require "components/footer.php"; ?>

  </body>

  </html>
