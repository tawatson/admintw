<?

class Tawatson_gitHook {
    private $pullInfo;
    private $dbh;
    private $git;
    private $repoName;

    public function __construct(Tawatson_PDOConnection $db, $repo, $repoName)
    {
      $this->dbh = $db;
      $this->git = $repo;
      $this->repoName = $repoName;
    }

    public function pull(){
      $pullInfo = $this->git->extractFromCommand('git pull');
      $pullInfo = explode(" ", $pullInfo[0]);
      $pullInfo = explode("..",$pullInfo[1]);
      $this->dbh->query("UPDATE wa_repos SET commit_id = :commitId WHERE repo_name = :repoName");
      $this->dbh->bind(":commitId", $pullInfo[1]);
      $this->dbh->bind(":repoName", $this->repoName);
      if($this->dbh->execute()){
        return true;
      } else {
        return false;
      }
    }

    public function getLatestCommit(){
      $this->dbh->query("SELECT * FROM wa_repos WHERE repo_name = :repoName");
      $this->dbh->bind(":repoName", $this->repoName);
      $repo = $this->dbh->single();

      $githuburl = 'https://api.github.com/repos/'.$repo['repo_name'].'/branches/'.$repo['working_branch'];
      // Get cURL resource
      $curl = curl_init();
      // Set some options - we are passing in a useragent too here
      curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => $githuburl,
          CURLOPT_USERAGENT => 'Codular Sample cURL Request'
      ));
      // Send the request & save response to $resp
      $resp = curl_exec($curl);
      // Close request to clear up some resources
      curl_close($curl);

      $pullInfo = json_decode($resp, true);
      $commitInfo = explode("/",$pullInfo['commit']['html_url']);

      return substr($commitInfo[6], 0, 7);
    }

    public function getLocalCommit()
    {
      $this->dbh->query("SELECT commit_id FROM wa_repos WHERE repo_name = :repoName");
      $this->dbh->bind(":repoName", $this->repoName);
      $localCommit = $this->dbh->single();
      return $localCommit['commit_id'];
    }

    public function isUpToDate(){
      // Get Local Commit ID
      $localCommit = $this->getLocalCommit();

      //Get Remote Commit ID
      $remoteCommit = $this->getLatestCommit();
      if($localCommit == $remoteCommit){
        return true;
      } else {
        return false;
      }
    }

}
