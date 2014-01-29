<?php

namespace Snilius\Util;

class Paginator {
  private $itemsPerPage;
  private $pagesToDisplay;
  private $totalItems;
  private $totalPages;
  private $page;
  private $requsetVar = '?page=';

  /**
   * Paginator
   * @param int $totalPagea
   * @param int $pagesToDisplay
   */
  public function __construct($totalItems,$pagesToDisplay) {
    $this->totalPages = ceil($totalItems/$pagesToDisplay);
    $this->pagesToDisplay = $pagesToDisplay;
  }

  /**
   * Set request variable
   * @param string $requsetVar request variable
   */
  public function setRequestVar($requsetVar) {
    $this->requsetVar = '?'.$requsetVar.'=';
  }

  /**
   * Get pagination for current page
   * @param int $page current page
   * @return array pagination setup
   */
  public function getPagination($page) {
    $requsetVar = $this->requsetVar;
    $totalPages = $this->totalPages;
    $pagesToDisplay = $this->pagesToDisplay;
    $ret = array();
    $ret['first'] = ($page>1)?$requsetVar.'1':'#';
    $ret['prev'] = ($page>1)?$requsetVar.($page-1):'#';
    $ret['next'] = ($page<$totalPages)?$requsetVar.($page+1):'#';
    $ret['last'] = ($page<$totalPages)?$requsetVar.$totalPages:'#';

    $startPage = 1;
    if($page>($totalPages-($pagesToDisplay/2)))
      $startPage=$totalPages-$pagesToDisplay;
    else if($page>($pagesToDisplay/2))
      $startPage=$page-($pagesToDisplay/2);

    $startPage = ($startPage<0)?1:$startPage;

    $endPage = $startPage+$pagesToDisplay;
    if ($endPage>$totalPages)
      $endPage=$totalPages;

    for ($i=$startPage; $i <= $endPage; $i++)
      $ret['nav'][]=$requsetVar.$i;

    return $ret;
  }
}

?>
