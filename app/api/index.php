<?php

/**
 * User: victor
 * Date: 9/12/14
 * Time: 4:22 PM
 */
require_once '../../include.inc';

$app = new \Slim\Slim(array(
  'view' => new \Snilius\Stack\Api\DocsView(),
  'debug' => true
));
$app->add(new \Snilius\Stack\Api\AuthMiddleware());
$app->contentType('application/json');

function niceJson() {
  $app = \Slim\Slim::getInstance();
  $body = $app->request->getBody();

  $json = json_decode($body);
  if ($json == null){
    $app->halt(400,'{"status":"fail", "msg":"Malformed JSON request body"}');
  }
};

// Get item list or item with $id
$app->get('/items(/:id)', function($id = -1) use ($app){
  $api = new \Snilius\Stack\Api($app->user);

  if ($id != -1){
    $item = $api->getItem($id);
    if ($item == null)
      $app->halt(204);
    echo json_encode($item->toArray());
  }else {
    $query = $app->request->get('query', null);
    $page = $app->request->get('page', 1);
    $ipp = $app->request->get('itemsPerPage', 30);
    $sort = $app->request->get('sort', 'DESC');
    $tags = $app->request->get('tags', 0);

    if (!is_numeric($page))
      $app->halt(400, '{"status": "fail", "msg": "Invalid page: ' . $page . '"}');

    if (!is_numeric($ipp))
      $app->halt(400, '{"status": "fail", "msg": "Invalid itemsPerPage: ' . $ipp . '"}');

    if (!in_array(strtolower($sort), array('asc', 'desc')))
      $app->halt(400, '{"status": "fail", "msg": "Invalid sort order: ' . $sort . ', should be either ASC or DESC"}');

    if (!is_numeric($tags))
      $app->halt(400, '{"status": "fail", "msg": "Invalid tags value: ' . $tags . ', should be either 0 or 1"}');

    $items = $api->getItems($query, $page, $ipp, $sort, $tags);
    if (count($items) < 1)
      $app->halt(204);
    echo '{"status":"ok", "list":'.json_encode($items).'}';
  }
});

// add new item
$app->post('/items', 'niceJson', function() use ($app){
  $body = $app->request->getBody();
  $api = new \Snilius\Stack\Api($app->user);
  $id = $api->newItem($body);
  $app->status(201);
  echo '{"status":"ok", "itemId": '.$id.'}';;
});

// update existing item
$app->put('/items/:id', 'niceJson', function($id) use ($app){
  $body = $app->request->getBody();
  $api = new \Snilius\Stack\Api($app->user);
  $result = $api->updateItem($id, $body);
  if ($result)
    $app->status(200);
  else
    $app->status(204);
});

// delete item
$app->delete('/items/:id', function($id) use ($app){
  $api = new \Snilius\Stack\Api($app->user);
  $result = $api->deleteItem($id);
  if ($result)
    $app->status(200);
  else
    $app->status(204);
});

$app->get('/ping', function() {
  echo '{"status": "ok", "ping": "pong"}';
});

$app->get('/auth', function() use ($app){
  $user = $app->user;
  $body = "Authorized as ".$user->getName();
  echo '{"status": "ok", "msg":"'.$body.'"}';
});

$app->get('/docs', function() use ($app){
  $app->contentType('text/html');
  $app->render('docs_orWhatever');
});

$app->any('/', function() use ($app){
  $app->contentType('text/html');
  echo '<span style="font-family: monospace">View up-to-date docs at https://victorhaggqvist.github.io/linkstack
        or the bundled docs at <a href="./docs">/docs</a></span>';
});

$app->run();
?>
