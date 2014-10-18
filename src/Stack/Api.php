<?php
/**
 * User: victor
 * Date: 9/14/14
 * Time: 1:08 PM
 */

namespace Snilius\Stack;


class Api {

  private $user;

  public function __construct(User $user) {
    $this->user = $user;
  }


  /**
   * Insert  new item
   * @param $postBody
   * @return string
   */
  public function newItem($postBody) {
    $json = json_decode($postBody, true);
    $url = $json['url'];
    $title = $json['title'];
    $tags = $json['tags'];
    $item = StackItem::create($url, $title, $tags);
    $item->setUser($this->user);
    $item->commit();
    return $item->getId();
  }

  /**
   * Get list of items
   * @param $query search query
   * @param $page page to get
   * @param $itemsPerPage number of items to get
   * @param $sort sort order ASC or DESC
   * @param $tags if query only tags
   * @return array
   */
  public function getItems($queryString = null, $page = 1, $itemsPerPage = 30, $sort = 'DESC', $tags = 0) {
    $count = $this->user->getItems()->count();
    $limit = $itemsPerPage*$page <= $count? $itemsPerPage*($page-1) : $count-$itemsPerPage;
    $limit = $limit < 0?0:$limit;
    $entityManager = $GLOBALS['entityManager'];

    if ($queryString == null) {
      $items = $entityManager->getRepository('Snilius\Stack\StackItem')
        ->findBy(
          array('user' => $this->user->getId()),
          array('modified' => $sort),
          $itemsPerPage,
          $limit
        );
      $resp = array();

      foreach ($items as $item) {
        $resp[] = $item->toArray();
      }
      return $resp;
    } else {
      // split up query terms
      $qsplit = array_values(array_filter(preg_split("/[,\s]/", $queryString)));
      $wildquery='';

      // construct query for only tags field or all the things
      if ($tags == 1){
        for ($i=0; $i < count($qsplit); $i++) {
          $wildquery .= 'i.tags LIKE :p'.$i;
          if ($i<count($qsplit)-1)
            $wildquery .= ' OR ';
        }
      }else {
        for ($i = 0; $i < count($qsplit); $i++) {
          $wildquery .= 'i.tags LIKE :p' . $i . ' OR i.url LIKE :p' . $i . ' OR i.title LIKE :p' . $i;
          if ($i < count($qsplit) - 1)
            $wildquery .= ' OR ';
        }
      }

      // build the DQL
      $dql = "SELECT i FROM Snilius\Stack\StackItem i WHERE i.user = :user AND (".$wildquery.") ORDER BY i.modified ".$sort;
      $query = $entityManager->createQuery($dql);
      $query->setFirstResult($limit);
      $query->setMaxResults($itemsPerPage);
      $query->setParameter('user', $this->user->getId());

      // put query terms in to SQL query
      for ($i=0; $i < count($qsplit); $i++) {
        $query->setParameter('p'.$i, '%'.$qsplit[$i].'%');
      }

      // Get the result and extract the good part
      $items = $query->getResult();
      $resp = array();
      foreach ($items as $item) {
        $resp[] = $item->toArray();
      }
      return $resp;
    }
  }

  /**
   * Get item with $id if $user owns it
   * @param $id
   * @return StackItem
   */
  public function getItem($id) {
    $entityManager = $GLOBALS['entityManager'];
    $args = array(
      'id' => (int) $id,
      'user' => $this->user->getId()
    );
    return $entityManager->getRepository('Snilius\Stack\StackItem')->findOneBy($args);
  }

  /**
   * Update item with $id to $putBody
   * @param $id
   * @param $putBody
   * @return bool
   */
  public function updateItem($id, $putBody){
    $item = $this->getItem($id);
    if ($item == null)
      return false;

    $json = json_decode($putBody, true);
    $url = $json['url'];
    $title = $json['title'];
    $tags = $json['tags'];

    $item->setUrl($url);
    $item->setTitle($title);
    $item->setTags($tags);
    $item->commit();
    return true;
  }

  public function deleteItem($id) {
    $item = $this->getItem($id);
    if ($item == null)
      return false;

    $item->delete();
    return true;
  }
}
