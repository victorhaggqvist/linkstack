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

  public function newItem($url,$title,$tags,$userid) {
    $sql = "INSERT INTO item (url,title,tags,user_id)VALUES(?,?,?,?)";

    // [fix] - remove empty tags ,, tag
    //trim tags
    $t = explode(",", $tags);
    array_map("trim",$t);
    $tags = implode(",", $t);

    if (!$this->hasUri($url)) {
      $url = 'http://'.$url;
    }

    if($this->pdo->prepExec($sql,array($url,$title,$tags,$userid))[0]==1)
      return true;
    else
      return false;
  }


  public function deleteItem($id) {
    $sql = "DELETE FROM item WHERE id=?";
    if($this->pdo->prepExec($sql,array($id))[1]==1)
      return true;
    else
      return false;
  }

  public function hasUri($url) {
    $regex = preg_match('/([a-öA-Ö]+)([:]{1})([\/]{2})(.+)/', $url);
    return $regex;
  }

  /*
  function url_exists($url) {
    if (!$fp = curl_init($url)) return false;
    return true;
  }*/
}

?>
