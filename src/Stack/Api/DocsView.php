<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 9/15/14
 * Time: 10:35 PM
 */

namespace Snilius\Stack\Api;


class DocsView extends \Slim\View {

  public function render($template) {
    $f = file_get_contents(TEMPLATES_PATH."/linkstack-api.html");
    return $f;
  }
} 
