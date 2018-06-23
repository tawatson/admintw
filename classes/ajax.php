<?php
session_start();
require "../config.php";

use Cz\Git\GitRepository;

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

  default:
    # code...
    break;
}

 ?>
