<?php

namespace Snilius;

use Snilius\Util\PDOHelper;
class StashManager {
  
  private $pdo;
  
  function __construct($pdo) {
    $this->pdo = $pdo;
  }
  
  function getItems($count) {
    $sql = "SELECT url, title, timestamp FROM stash ORDER BY timestamp LIMIT 0,".$count;
    
    $items = $this->pdo->justQuery($sql)[2];
    return $items;
  }
  
  function tagsToLinks($tags){
    $t = explode(',', $tags);
    $tt = array_map('trim', $t);
    
    $links = '';
    foreach ($tt as $i){
      // [fix] - Make it return bootstrap lables
      $links.='<a href="browse.php?tag='.urlencode($i).'">'.$i.'</a>';
    }
    
    return $links;
  }
}
?>