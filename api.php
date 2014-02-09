<?php

use Snilius\StashApi;

require_once 'core/include.inc';
require_once 'core/classes/StashApi.php';

$token = $_REQUEST['token'];
$method = @$_REQUEST['method'];

if (!isset($method)) {
  die('method is required');
}

$api = new StashApi();

if (isset($method) && $method=='login') {
  $provider = @$_GET['provider'];
      $api->login($provider);
}elseif(isset($method) && $method=='ping'){
  header('Content-Type: application/json');
  echo '{"status": "ok", "ping": 1}';
}

if (strlen($token)==64 && isset($method)) {


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
      header('Content-Type: application/json');
  	  $page = @$_GET['page'];
      $query = @$_GET['query'];
      $tags = @$_GET['tags']; // should be either 1 or 0
      $api->getList($page,$query,$tags);
  	  break;

  	case 'delete':
  	  $id = $_POST['id'];
  	  $api->delete($id);
  	  break;

  	default:
  	  break;
  }
}elseif (isset($_GET['logincallback'])) {

}
?>
