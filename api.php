<?php

use Snilius\StashApi;

require_once 'core/config.inc';
require_once 'core/classes/PDOHelper.php';
require_once 'core/classes/StashApi.php';
require_once 'core/classes/StashItem.php';
require_once 'core/classes/StashManager.php';
require_once 'core/classes/UserManager.php';

$token = $_POST['token'];
$method = $_POST['method'];

if (strlen($token)==64 && isset($method)) {
  $api = new StashApi();

  if(!$api->validateToken($token))
    die();

  switch ($method) {
  	case 'new': //new item
  	  $url = $_POST['url'];
  	  $title = $_POST['title'];
  	  $tags = $_POST['tags'];

  	  $api->newItem($url, $title, $tags);
  	  break;

  	case 'list': //get item list
  	  $start = $_POST['start'];
  	  $count = $_POST['count'];
  	  break;

  	case 'delete':
  	  $id = $_POST['id'];
  	  $api->delete($id);
  	  break;
  	default:
  	  break;
  }
}
?>
