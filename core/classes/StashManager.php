<?php

namespace Snilius;

class StashManager {

  private $pdo
         ,$userid
        //Default isems per page
         ,$itemsPerPage = 30;

  public function __construct($pdo) {
    $this->pdo = $pdo;
  }

  /**
   * Set user id for session
   * @param int $userid user id
   */
  public function setUserid($userid) {
    $this->userid = $userid;
  }

  /**
   * Ovverride default items per page
   * @param int $itemsPerPage number of items per page
   */
  public function setItemsPerPage($itemsPerPage) {
    $this->itemsPerPage = $itemsPerPage;
  }

  /**
   * Get $results number of items from $start
   * @param int $results
   * @param int $start
   */
  public function getItems($results,$start=0) {
    $sql = "SELECT id, url, title, timestamp, tags, user_id FROM item WHERE user_id=:userid ORDER BY timestamp DESC LIMIT :start, :results";
    $args = array('userid'=>$this->userid);
    return $this->pdo->prepQueryLimit($sql,$args,$start,$results);
  }

  /**
   * Get items on page $page
   * @param  int $page page
   * @return [type]       [description]
   */
  public function getItemsPage($page) {
    $start = ($page==1)?0:$this->itemsPerPage*($page-1);
    return $this->getItems($this->itemsPerPage,$start);
  }

  /**
   * Get linkset from string of tags
   * @param string $tags tag1,tag2,tagN
   * @return string HTML a elements for $tags
   */
  public function tagsToLinks($tags){
    $t = explode(',', $tags);
    $tt = array_map('trim', $t);

    $links = '';
    foreach ($tt as $i){
      // [fix] - Make it return bootstrap lables
      $links.='<a href="browse.php?tag='.urlencode($i).'">'.$i.'</a>';
    }

    return $links;
  }

  /**
   * Get total number of items for user in session
   */
  public function getCount(){
    $sql = "SELECT COUNT(*) as count FROM item WHERE user_id=?";
    return $this->pdo->prepQuery($sql,array($this->userid))[2][0]['count'];
  }

  /**
   * Perform a query on a users stack
   * @param  string $query query
   * @param  bool $tags  if to search only tags
   * @param  int $page  limit the search to a page
   * @return array  resultset
   */
  public function queryItemsPage($query, $tags, $page) {
    $start = ($page==1)?0:$this->itemsPerPage*($page-1);
    $qsplit = array_values(array_filter(preg_split("/[,\s]/", $query))); //array_filter: remove empty, array_values: reindex
    // [review] - There might be a good idea to escape creepe chars and anly allow alphanumerics...

    $wildquery='';
    $sql='';
    if ($tags) {
      for ($i=0; $i < count($qsplit); $i++) { //escape stuff and merge
        $quote = trim($this->pdo->quote($qsplit[$i]),'\''); //triming gets rid of outer '', cuz they should be outside %%
        $wildquery .= '`tags` LIKE \'%'.$quote.'%\'';
        if ($i<count($qsplit)-1)
          $wildquery .= ' OR ';
      }
    }else {
      for ($i=0; $i < count($qsplit); $i++) { //escape stuff and merge
        $quote = trim($this->pdo->quote($qsplit[$i]),'\''); //triming gets rid of outer '', cuz they should be outside %%
        $wildquery .= '`tags` LIKE \'%'.$quote.'%\' OR `url` LIKE \'%'.$quote.'%\' OR `title` LIKE \'%'.$quote.'%\'';
        if ($i<count($qsplit)-1)
          $wildquery .= ' OR ';
      }
    }

    $sql = 'SELECT id, url, title, timestamp, tags, user_id FROM item WHERE user_id=:userid AND ('.$wildquery.') ORDER BY timestamp DESC LIMIT :start, :results';
    $args = array('userid'=>$this->userid);
    return $this->pdo->prepQueryLimit($sql,$args,$start,$this->itemsPerPage);
  }
}
?>
