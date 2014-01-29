<?php

define('ASSETS_PATH', 'assets/');
define('CORE_PATH', 'core/');
define('TEMPLATES_PATH', ASSETS_PATH.'templates/');
// define('OPENID_CALLBACK', "http://stack.snilius.com");
define('OPENID_CALLBACK', "http://localhost/stash");

$db_conf=new stdClass;
$db_conf->host="localhost";
$db_conf->db="linkstash";
$db_conf->user="root";
$db_conf->pass="root";

?>
