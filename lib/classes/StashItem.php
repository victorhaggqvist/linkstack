<?php

namespace Snilius;

use Snilius\Util\PDOHelper;
/**
 * StashItem
 * @author victor
 *
 */
class StashItem {

  private $pdo;
  
  function __construct($pdo){
    $this->pdo = $pdo;
  }
  
  function newItem($url,$title,$tags) {
    $sql = "INSERT INTO stash (url,title,tags,timestamp)VALUES(?,?,?,?)";
    
    if($this->pdo->prepExec($sql,array($url,$title,$tags,date('Y-m-d H:i:s')))[0]==1)
      return true;
    else 
      return false;
  }
  
  private function fetchFavIcon($url) {
    print_r($this->url_exists($url));
    
    
    $html = new \simple_html_dom();
    $web = new \WebBrowser();
    $result = $web->Process($url);
    if (!$result["success"])
      echo "Error retrieving URL.  " . $result["error"] . "\n";
    else if ($result["response"]["code"] != 200)
      echo "Error retrieving URL.  Server returned:  " . $result["response"]["code"] . " " . $result["response"]["meaning"] . "\n";
    //actual stuff
    else
    {
      //load page body to html 'object'
      print_r($result);
      $html->load($result["head"]);
      
      $a = $html->find('link[rel]');
      print_r($a);
    }
  }
  
  function url_exists($url) {
    if (!$fp = curl_init($url)) return false;
    return true;
  }
}

?>
