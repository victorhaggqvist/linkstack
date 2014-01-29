<?php

require_once 'core/include.inc';

use Snilius\OpenID\UserManager;

try {
  # Change 'localhost' to your domain name.
  $openid = new LightOpenID(OPENID_CALLBACK);
  if(!$openid->mode) {
    $openid->required = array('contact/email');
    $openid->identity = 'https://www.google.com/accounts/o8/id';
    header('Location: ' . $openid->authUrl());
  } elseif($openid->mode == 'cancel') {
    echo 'User has canceled authentication!';
  } else {

    if ($openid->validate()){
      $userManager = new UserManager();
      if (!$userManager->userExists($openid->identity))
        $userManager->createUser($openid);

      $userManager->createSession();

      header('Location: ./');
    }

    echo 'User ' . ($openid->validate() ? $openid->identity . ' has ' : 'has not ') . 'logged in.';
    echo '<br>'.$openid->data['openid_op_endpoint'];
    echo '<br>'.$openid->getAttributes()['contact/email'];
  }
} catch(ErrorException $e) {
  echo $e->getMessage();
}
?>
