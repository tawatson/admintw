<?php
session_start();
require "config.php";

use Cz\Git\GitRepository;
switch ($_SERVER['REQUEST_METHOD']) {
  case 'POST':

  switch ($_POST['action']) {
    case 'git_pull':
        // GET REPO INFO FROM DB
        $db->query("SELECT * from wa_repos WHERE id = :id");
        $db->bind(":id", $_POST['repo']);
        $repo = $db->single();

        // CREATE INSTANCE OF REPO CLASS
        $git = new Tawatson_gitHook($db, new GitRepository($repo['local_dir']),$repo['repo_name']);
        if($git->pull()){echo "1";};
    break;

    case 'createInvoice':
      $db->query("SELECT id from wa_clients WHERE name = :name");
      $db->bind(":name", $_POST['client']);
      $result = $db->single();

      $db->query("INSERT INTO wa_invoices (client_id, issue_date, due_date) VALUES(:client, :issue_date, :due_date)");
      $db->bind(":client",$result['id']);
      $db->bind(":issue_date",date("Y-m-d H:i:s",strtotime(str_replace('/', '-',$_POST['issueDate']))));
      $db->bind(":due_date",date("Y-m-d H:i:s",strtotime(str_replace('/', '-',$_POST['dueDate']))));
      if($db->execute()){
        echo $db->lastInsertId();
      }

      break;

    default:
      # code...
      break;
  }

    break;

    case 'GET':
    $term="%".$_GET["q"]."%";
      $db->query("SELECT name FROM wa_clients WHERE name LIKE :term");
      $db->bind(":term", $term);
      $results = $db->resultSet();

      $json=array();

    foreach ($results as $row){
        array_push($json, $row['name']);
      }

      echo json_encode($json);

    break;
      break;

  default:
    // code...
    break;
}



 ?>
