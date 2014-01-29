<?php
use Snilius\OpenID\UserManager;

$userMan = new UserManager();
$user='';
if ($userMan->checkSession())
  $user = $userMan->getUser();
?>
