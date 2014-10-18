<?php
/**
 * User: victor
 * Date: 9/12/14
 * Time: 5:33 PM
 */

namespace Snilius\Login;

/**
 * Class Session
 * @package Snilius\Login
 * @Entity @Table(name="sessions")
 */
class Session {

  /** @Id @Column(type="integer") @GeneratedValue */
  protected $id;

  /** @Column(type="integer")  */
  protected $userId;

  /** @Column(type="string")  */
  protected $token;

  /** @Column(type="datetime")  */
  protected $expire;

  /** @Column(type="string")  */
  protected $ip;

  /**
   * @param $ip
   * @param $userId
   */
  function __construct($userId) {
    $this->ip = $_SERVER['REMOTE_ADDR'];
    $this->userId = $userId;
    $this->token = $this->makeToken();
    $this->expire = $this->makeTime();
  }

  private function makeToken() {
    $char = "qwertyuiopasdfghjklzxcvbnm1234567890";
    $chars = strlen($char);
    $token = "";
    for ($i = 0; $i < 54; ++$i){
      $index = mt_rand(0, $chars);
      $token .= substr($char, $index, 1);
    }
    return $token;
  }

  private function makeTime(){
    $dateTime = new \DateTime();
    $dateTime->setTimestamp(time() + SESSION_TIMEOUT);
    return $dateTime;
  }

  /**
   * Find a session by token
   * @param string $token
   * @return /Snilius/Login/Session | null
   */
  public static function  getByToken($token){
    $entityManager = $GLOBALS['entityManager'];
    $session = $entityManager->getRepository('Snilius\Login\Session')->findOneBy(array('token' => $token));
    if (!$session)
      return null;
    return $session;
  }

  /**
   * Update the expiration time
   */
  public function renew(){
    $this->expire = $this->makeTime();
    $this->commit();
  }

  /**
   * Persists changes
   */
  public function commit() {
    $entityManager = $GLOBALS['entityManager'];
    $entityManager->persist($this);
    $entityManager->flush();
  }

  /**
   * Sets set session cookie
   */
  public function bakeCookie() {
    setcookie(SESSION_COOKIE_NAME, $this->token, $this->expire->getTimestamp());
  }

  /**
   * @return mixed
   */
  public function getUserId() {
    return $this->userId;
  }

  /**
   * Delete session
   */
  public function delete(){
    setcookie(SESSION_COOKIE_NAME, '', time()-30);
    $entityManager = $GLOBALS['entityManager'];
    $entityManager->remove($this);
    $entityManager->flush();
  }
}
