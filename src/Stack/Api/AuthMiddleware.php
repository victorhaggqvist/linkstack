<?php

namespace Snilius\Stack\Api;

class AuthMiddleware extends \Slim\Middleware {

  public function call() {
    // Get reference to application
    $app = $this->app;

    // pass through requests
    $path = $app->request->getPathInfo();
    if ($path == "/ping" || $path == "/" || $path == "/docs"){
      $this->next->call();
      return;
    }

    $key = $app->request->get('key');
//    if (!$key)
//      $key = $app->request->post('key');

    $timestamp = $app->request->get('timestamp');

    $user = \Snilius\Stack\User::getByApikey($key, $timestamp);
    if ($user) {
      $app->user = $user;
      $this->next->call();
    }else{
      $app->status(401);
      echo '{
              "status": "fail",
              "code": 401,
              "msg": "Unauthorized aka. invalid api key"
            }';
    }
  }
}

?>
