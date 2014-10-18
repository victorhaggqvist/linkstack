<?php

namespace Snilius\Stack;

/**
 * User: victor
 * Date: 9/10/14
 * Time: 10:19 PM
 * @Entity @Table(name="items")
 */
class StackItem {

  /** @Id @Column(type="integer") @GeneratedValue */
  protected $id;

  /** @Column(type="string")   */
  protected $url;

  /** @Column(type="string")   */
  protected $title;

  /** @Column(type="string")   */
  protected $tags;

  /** @ManyToOne(targetEntity="User", inversedBy="items")  */
  protected $user;

  /** @Column(type="datetime") */
  protected $created;

  /** @Column(type="datetime") */
  protected $modified;

  /**
   * @return mixed
   */
  public function getTags() {
    return $this->tags;
  }

  /**
   * @param mixed $tags
   */
  public function setTags($tags) {
    $t = explode(",", $tags);
    array_map("trim",$t);
    $tags = implode(",", $t);
    $this->tags = $tags;
  }

  /**
   * @return mixed
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * @param mixed $title
   */
  public function setTitle($title) {
    $this->title = $title;
  }

  /**
   * @return mixed
   */
  public function getUrl() {
    return $this->url;
  }

  /**
   * @param mixed $url
   */
  public function setUrl($url) {
    if (!$this->hasUri($url)) {
      $url = 'http://'.$url;
    }
    $this->url = $url;
  }

  /**
   * @return mixed
   */
  public function getUser() {
    return $this->user;
  }

  /**
   * @return mixed
   */
  public function getId() {
    return $this->id;
  }

  public static function create($url,$title,$tags) {
    $item = new StackItem();
    $item->setUrl($url);
    $item->setTitle($title);
    $item->setTags($tags);
    $item->created = new \DateTime();
    $item->modified = $item->created;
    return $item;
  }

  private function hasUri($url) {
    $regex = preg_match('/([a-öA-Ö]+)([:]{1})([\/]{2})(.+)/', $url);
    return $regex;
  }


  public function addTag($tag){
    $this->tags += ","+ trim($tag);
  }

  /**
   * @param \Snilius\Stack\User $owner
   */
  public function setUser($user){
    $user->addOwnedItem($this);
    $this->user = $user;
  }

  /**
   * Persist item
   */
  public function commit() {
    if (!isset($this->created))
      $this->created = new \DateTime();
    $this->modified = new \DateTime();

    $entityManager = $GLOBALS['entityManager'];
    $entityManager->persist($this);
    $entityManager->flush();
  }

  /**
   * Delete item
   */
  public function delete(){
    $entityManager = $GLOBALS['entityManager'];
    $entityManager->remove($this);
    $entityManager->flush();
  }

  /**
   * @return mixed
   */
  public function getModified() {
    return $this->modified;
  }

  /**
   * @return mixed
   */
  public function getCreated() {
    return $this->created;
  }

  /**
   * Get array of item
   * @return array
   */
  public function toArray() {
    $itm['id'] = $this->getId();
    $itm['url'] = $this->getUrl();
    $itm['title'] = $this->getTitle();
    $itm['timestamp'] = $this->getModified()->format("Y-m-d H:i:s");
    $itm['created'] = $this->getCreated()->format("Y-m-d H:i:s");
    $itm['tags'] = $this->getTags();
    $itm['user_id'] = $this->user->getId();
    return $itm;
  }
}
