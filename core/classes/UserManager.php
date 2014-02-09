<?php

namespace Snilius\OpenID;

use Snilius\Util\PDOHelper;

class UserManager {

  private $pdo
         ,$openid_identity
         ,$token;

  function __construct() {
    $this->pdo = new PDOHelper($GLOBALS['db_conf']);
  }

  /*public function validateUser($openid){
    if ($this->userExists($openid->identity)) {
      $this->createSession($openid->identity);
    }else{
      $this->createUser($openid);
    }
  }*/

  /**
   * Check if user exists based on OpenID identity
   * @param string $openidIdentity
   * @return boolean Of existens
   */
  public function userExists($openidIdentity) {
    $this->openid_identity = $openidIdentity;
    $sql = 'SELECT openid_identity FROM user WHERE openid_identity=?';
    $res = $this->pdo->prepQuery($sql, array($openidIdentity));

    if ($res[1]==1)
      return true;
    else
      return false;
  }

  /**
   * Create a session based on existing users openid identity
   */
  public function createSession(){
    $sql = "SELECT `token` FROM user WHERE `openid_identity`=?";
    $res = $this->pdo->prepQuery($sql, array($this->openid_identity));

    $token = $res[2][0]['token'];
    // [review] - Might not be the best idea to use users OpenID identity as identifyer
    setcookie('token',$token,mktime()+60*60);
  }
  /**
   * Create new user based on LightOpenID object
   * @param LightOpenID object $openid
   */
  public function createUser($openid) {
    $sql = 'INSERT INTO user (openid_identity,openid_provider,email,token)VALUES(?,?,?,?)';

    $hash = hash('sha256',$openid->identity);

    $this->pdo->prepExec($sql,array($openid->identity,
                                    $openid->data['openid_op_endpoint'],
                                    $openid->getAttributes()['contact/email'],
                                    $hash));
  }

  /**
   * Check if there is an valid session
   * @return boolean
   */
  public function checkSession(){
    if (isset($_COOKIE['token'])) {
      $this->token = $_COOKIE['token'];
      $sql = 'SELECT token FROM user WHERE token=?';

      $res = $this->pdo->prepExec($sql, array($this->token));
      if ($res[1]==1){
        setcookie('token',$this->token,mktime()+60*60);
        return true;
      }else
        return false;
    }else
      return false;
  }

  /**
   * Get user data based on previously enter $token
   * @return array User meta
   */
  public function getUser() {
    $sql = 'SELECT openid_identity, id, email FROM user WHERE token=?';
    $res = $this->pdo->prepQuery($sql, array($this->token));
    return $res[2][0];
  }

  /**
   * Check if token exists
   * @param string $token
   * @return boolean Of existens
   */
  public function tokenExixts($token) {
    $this->token = $token;
    $sql = "SELECT token FROM user WHERE token=?";
    $res = $this->pdo->prepQuery($sql, array($token));
    if ($res[1]==1)
      return true;
    else
      return false;
  }
}

?>
