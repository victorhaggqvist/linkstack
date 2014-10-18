<?php
require_once '../include.inc';

$sm = new \Snilius\Login\SessionManager();

$sm->killSession();
header('Location: ./');
?>
