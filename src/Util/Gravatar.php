<?php

namespace Snilius\Util;

/**
 *
 * @author victor
 *
 */
class Gravatar {
  /**
   * Make Gravatar URL
   * @param string $email
   * @param string $type
   * @param string $size
   * @return string avatar url
   */
  public static function getAvatar($email,$size='',$type='') {
    $hash = md5(strtolower(trim($email)));
    $url = 'http://www.gravatar.com/avatar/'.$hash;

    if ($type!='')
      $url.='.'.$type;
    if ($size!='')
      $url.='?s='.$size;

    return $url;
  }
}

?>
