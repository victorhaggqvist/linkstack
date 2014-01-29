<?php

require_once 'core/include.inc';
require_once TEMPLATES_PATH.'html_head.php';

use Snilius\Util\PDOHelper;
use Snilius\StashItem;
use Snilius\Util\Bootstrap\Alert;

$pdo = new PDOHelper($db_conf);

?>
    <body>
    <?php require_once TEMPLATES_PATH.'header.php';?>

    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <?php
          if (is_array($user)) {
            require_once TEMPLATES_PATH.'pushform.php';
          }else{
            require_once TEMPLATES_PATH.'frontpage.php';
          }
          ?>
        </div><!-- /.col-lg-8 -->
        <div class="col-lg-4">
          <!-- [todo] - bulk push here -->
        </div><!-- /.col-lg-4 -->
      </div><!-- /.row -->

      <?php if (is_array($user)) { ?>
      <div class="row">
        <div class="col-lg-12">
          <?php require_once TEMPLATES_PATH.'recentstack.php'; ?>
        </div>
      </div><!-- /.row -->
      <?php } ?>
    </div>

    <?php require_once TEMPLATES_PATH.'footer.php'; ?>
    </body>
</html>
