<?php
/**
 * User: victor
 * Date: 9/22/14
 * Time: 12:44 AM
 */

namespace Snilius\Stack;


class StackManager {

  private $user;

  function __construct($user) {
    $this->user = $user;
  }

//  public function getItems($userId, $count, $sort, $query = null, $page = 1, $tags = 0) {
//    $entityManager = $GLOBALS['entityManager'];
//    $items = $entityManager->getRepository('Snilius\Stack\StackItem')
//      ->findBy(
//        array('user' => $userId),
//        array('modified' => $sort),
//        $count
//      );
//    return $items;
//  }

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
}
