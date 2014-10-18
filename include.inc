<?php

use \Doctrine\ORM\Tools\Setup;
use \Doctrine\ORM\EntityManager;

require_once 'config.inc';
require_once 'vendor/autoload.php';

$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), DEVMODE);

$entityManager = EntityManager::create($dbParams, $config);

if ($_SERVER["PHP_SELF"] != ROOT_DIR."api/index.php" && PHP_SAPI != 'cli'){
  require_once 'app/verifyuser.php';
}

?>
