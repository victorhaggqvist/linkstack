<?php

namespace Snilius;

use Snilius\Util\PDOHelper;
use Snilius\OpenID\UserManager;
class StashApi {
  
  private $token
         ,$pdo
         ,$user;
  
  function __construct() {
    $this->pdo = new PDOHelper($GLOBALS['db_conf']);
  }
  
  /**
   * Check if token is valid
   * @param string $token
   * @return boolean
   */
  public function validateToken($token) {
    $this->token = $token;
    $userManager = new UserManager();
    if ($userManager->tokenExixts($token)){
      $this->user = $userManager->getUser();
      return true;
    }else 
      return false;
  }
  
  /**
   * Add item to stash
   * @param string $url
   * @param string $title
   * @param string $tags
   */
  public function newItem($url,$title,$tags) {
    $stashItem = new StashItem($this->pdo);
    if($stashItem->newItem($url, $title, $tags, $this->user['id']))
      echo "1"; //ACK item
  }
  
  /**
   * Detete item from stash
   * @param int $id item id
   */
  public function delete($id) {
    $stashItem = new StashItem($this->pdo);
    if($stashItem->deleteItem($id))
      echo '1'; //ACK item
  }
  
  function __destruct() {
    $this->pdo=null;
  }
}

?>