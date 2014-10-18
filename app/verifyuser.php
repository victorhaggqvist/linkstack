<?php

$sessionManager = new \Snilius\Login\SessionManager();

$user = null;

if ($sessionManager->checkSession()){
  $user = $sessionManager->getUser();
}

?>
