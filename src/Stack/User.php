<?php

namespace Snilius\Stack;

use \Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @Entity @Table(name="users")
 */
class User {
  /** @Id @Column(type="integer") @GeneratedValue */
  protected $id;

  /** @Column(type="string")   */
  protected $oauthId;

  /** @Column(type="string")   */
  protected $email;

  /** @Column(type="string")   */
  protected $name;

  /** @Column(type="string")   */
  protected $pictureUrl;

  /** @OneToMany(targetEntity="StackItem", mappedBy="user") */
  protected $items;

  public function __construct($oauthId, $email, $name, $pictureUrl) {
    $this->items = new ArrayCollection();
    $this->oauthId = hash('sha256', $oauthId.self::salt($oauthId));
    $this->email = $email;
    $this->name = $name;
    $this->pictureUrl = $pictureUrl;
  }

  public static function getById($userId) {
    $entityManager = $GLOBALS['entityManager'];
    $user = $entityManager->getRepository('Snilius\Stack\User')->findOneBy(array('id' => $userId));
    if (!$user)
      return null;
    return $user;
  }

  public static function getByOAuthId($oauthId) {
    $oauthId = hash('sha256', $oauthId.self::salt($oauthId));
    $entityManager = $GLOBALS['entityManager'];
    $user = $entityManager->getRepository('Snilius\Stack\User')->findOneBy(array('oauthId' => $oauthId));
    if (!$user)
      return null;
    return $user;
  }

  public static function getByOauthHash($key) {
    $entityManager = $GLOBALS['entityManager'];
    $user = $entityManager->getRepository('Snilius\Stack\User')->findOneBy(array('oauthId' => $key));
    if (!$user)
      return null;
    return $user;
  }

  public static function getByApikey($key, $timestamp){
    if ((time()-$timestamp) > APIKEY_LIFE)
      return null;

    $entityManager = $GLOBALS['entityManager'];
    $users = $entityManager->createQuery('select u from Snilius\Stack\User u');
    $iterUsers = $users->iterate();

    foreach ($iterUsers as $row) {
      $user = $row[0];
      $oid = $user->getOauthId();
      $apikey = hash('sha256',$oid.$timestamp);
      if ($key == $apikey)
        return $user;
    }
    return null;
  }

  public function getApiCredentials(){
    $timestamp = time();
    $key =  hash('sha256',$this->oauthId.$timestamp);

    return array('timestamp' => $timestamp, 'key' => $key);
  }

  public function addOwnedItem($item){
    $this->items[] = $item;
  }

  public function commit() {
    $entityManager = $GLOBALS['entityManager'];
    $entityManager->persist($this);
    $entityManager->flush();
  }

  /**
   * @return mixed
   */
  public function getName() {
    return $this->name;
  }

  private static function salt($oauthId){
    return substr($oauthId, 0, 5);
  }

  /**
   * @return mixed
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @return mixed
   */
  public function getItems() {
    return $this->items;
  }

  /**
   * @return mixed
   */
  public function getEmail() {
    return $this->email;
  }

  /**
   * @return mixed
   */
  public function getPictureUrl() {
    return $this->pictureUrl;
  }

  /**
   * @return mixed
   */
  public function getOauthId() {
    return $this->oauthId;
  }
}

 ?>
