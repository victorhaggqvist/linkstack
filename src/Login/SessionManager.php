<?php
/**
 * User: victor
 * Date: 9/12/14
 * Time: 5:34 PM
 */

namespace Snilius\Login;


use Snilius\Stack\User;

class SessionManager {

  private $session;

  public function checkSession(){
    if (!isset($_COOKIE[SESSION_COOKIE_NAME]))
      return false;

    $token = htmlspecialchars($_COOKIE[SESSION_COOKIE_NAME]);
    $session = Session::getByToken($token);

    if (!$session)
      return false;

    $this->session = $session;
    return true;
  }

  /**
   * Kill active session
   */
  public function killSession() {
    if (!isset($_COOKIE[SESSION_COOKIE_NAME]))
      return;

    $token = htmlspecialchars($_COOKIE[SESSION_COOKIE_NAME]);
    $session = Session::getByToken($token);

    if (!$session) {
      setcookie(SESSION_COOKIE_NAME, '', time()-30);
      return;
    }
    var_dump($session);
    $session->delete();
    return;
  }

  public function checkApiSession($key){
    $token = htmlspecialchars($key);
    $session = Session::getByToken($token);

    if (!$session)
      return false;

    $this->session = $session;
    return true;
  }

  public function getUser(){
    $user = User::getById($this->session->getUserId());
    return $user;
  }
}
