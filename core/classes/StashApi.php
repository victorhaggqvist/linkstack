<?php

namespace Snilius;

use Snilius\Util\PDOHelper;
use Snilius\OpenID\UserManager;
use Snilius\StashManager;

class StashApi {

  private $token
         ,$pdo
         ,$user;

  function __construct() {
    $this->pdo = new PDOHelper($GLOBALS['db_conf']);
  }

  /**
   * Check if token is valid
   * @param string $token
   * @return boolean
   */
  public function validateToken($token) {
    $this->token = $token;
    $userManager = new UserManager();
    if ($userManager->tokenExixts($token)){
      $this->user = $userManager->getUser();
      return true;
    }else
      return false;
  }

  /**
   * Add item to stash
   * @param string $url
   * @param string $title
   * @param string $tags
   */
  public function newItem($url,$title,$tags) {
    $stashItem = new StashItem($this->pdo);
    // [todo] - make it return json vith ACK and db id as json
    if($stashItem->newItem($url, $title, $tags, $this->user['id']))
      echo "1"; //ACK item
  }

  /**
   * Detete item from stash
   * @param int $id item id
   */
  public function delete($id) {
    $stashItem = new StashItem($this->pdo);
    if($stashItem->deleteItem($id))
      echo '1'; //ACK item
  }

  public function getList($page,$query,$tags) {
    $stackManager = new StashManager($this->pdo);
    $stackManager->setUserid($this->user['id']);
    $page = (!isset($page)||$page<1)?1:$page;


    if (!isset($query)) { // regular listing, if query not set
      $ret = $stackManager->getItemsPage($page);
    }else{ // do actual searching
      $ret = $stackManager->queryItemsPage($query,$tags,$page);
    }

    $return = '';
    if ($ret[0]==1) {
      $return = array('success' => '1','list'=>$ret[2]);
    }else{
      $return = array('success' => '0');
    }

    echo json_encode($return);
  }

  public function login($provider) {
    try {
      # Change 'localhost' to your domain name.
      $openid = new \LightOpenID(OPENID_CALLBACK);
      if(!$openid->mode) {
        $openid->required = array('contact/email');
        $openid->identity = 'https://www.google.com/accounts/o8/id';
        header('Location: ' . $openid->authUrl());
      } elseif($openid->mode == 'cancel') {
        echo 'User has canceled authentication!';
      } else {

        header('Content-Type: application/json');
        $return = array();

        if ($openid->validate()){
          $userManager = new UserManager();
          if (!$userManager->userExists($openid->identity))
            $userManager->createUser($openid);

          $return['success'] = 1;
          $return['hash'] = hash('sha256',$openid->identity);
          $return['email'] = $openid->getAttributes()['contact/email'];
          $return['provider'] = 'Google';
          // var_dump($userManager->getUser());
          // var_dump($openid);
        }else{
          $return['message'] = 'fail';
        }

        echo json_encode($return);

        // echo 'User ' . ($openid->validate() ? $openid->identity . ' has ' : 'has not ') . 'logged in.';
        // echo '<br>'.$openid->data['openid_op_endpoint'];
        // echo '<br>'.$openid->getAttributes()['contact/email'];
      }
    } catch(ErrorException $e) {
      echo $e->getMessage();
    }
    $userManager = new UserManager();
  }

  function __destruct() {
    $this->pdo=null;
  }
}

?>
