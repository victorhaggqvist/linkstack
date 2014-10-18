<?php

require_once '../include.inc';
require_once TEMPLATES_PATH.'/html_head.php';

use Snilius\Util\PDOHelper;
use Snilius\Util\Bootstrap\Alert;
use Snilius\Util\Paginator;
use Snilius\StashItem;
use Snilius\StashManager;

//$pdo = new PDOHelper($db_conf);
//$stashManager = new StashManager($pdo);

?>
    <body>
    <?php
    require_once TEMPLATES_PATH.'/header.php';
    if(!is_object($user))
      header('Location: ./');
    ?>

    <div class="container" style="margin-top: 20px;">
      <?php
      $sm  = new \Snilius\Stack\StackManager($user);
      $page = (isset($_GET['page']))?$_GET['page']:1;
      $itemsPerPage = (isset($_GET['itemsPerPage']))?$_GET['itemsPerPage']:30;
      $sort = (isset($_GET['sort']))?$_GET['sort']:'DESC';

      $items = null;
      $itemCount = $user->getItems()->count();

      if (isset($_GET['q'])&&strlen($_GET['q'])>0) {
        $q = $_GET['q'];
        $tags = (isset($_GET['tags']))?1:0;

        $items = $sm->getItems($q, $page, $itemsPerPage, $sort,$tags);

      }else {
        $items = $sm->getItems(null, $page, $itemsPerPage, $sort);
      }
      ?>
      <form class="form-horizontal" role="form" action="browse.php" method="get">
        <div class="row">
          <div class="col-lg-6">
            <div class="input-group">
              <input type="search" class="form-control" id="q" placeholder="Enter a tag, title or what ever you want" name="q" style="height: 33px;" value="<?= @$_GET['q']; ?>">
              <span class="input-group-btn">
                <button class="btn btn-default" type="submit">Search</button>
              </span>
            </div><!-- /input-group -->
          </div><!-- /.col-lg-6 -->
          <div class="col-lg-2">
            <div class="checkbox">
                <label>
                  <input type="checkbox" name="tags" <?php echo (@$_GET['tags']=='on')?'checked="checked"':''; ?>> Only tags
                </label>
              </div>
          </div>
          <div class="col-lg-2">
            <select class="form-control" name="itemsPerPage">
              <option disabled>Items per page</option>
              <option selected>30</option>
              <option>60</option>
              <option>100</option>
              <option>200</option>
            </select>
          </div>
        </div>
      </form>
      <div class="row">
        <div class="col-lg-12">
          <?php
            if ($itemCount>0) {
              $total = ceil($itemCount/$itemsPerPage);
              $tpage = ($total>1)?'pages':'page';
              echo "<div style=\"display: block; margin-top: 2px;\">Listing $itemCount links in $total $tpage</div>";

              $paging = new Paginator($itemCount,$itemsPerPage);
              $pages = $paging->getPagination($page);

              $urlParams = '';
              foreach($_GET as $key => $g){
                $urlParams .= "&$key=$g";
              }

              function makeLink($href, $text, $class=''){
                $cls=($class=='')?'':"class=\"$class\"";
                return "<li $cls><a href=\"$href\">$text</a>";
              }

              echo '<ul class="pagination" style="margin: 4px 0 0 0;">';
              echo makeLink($pages['prev'].$urlParams,'&laquo;', $pages['prev']=='#'?'disabled':'');
              foreach ($pages['nav'] as $key => $value) {
                if (($key+1)==$page)
                  echo makeLink($value.$urlParams,$key+1,'active');
                else
                  echo makeLink($value.$urlParams,$key+1);
              }
              echo makeLink($pages['next'].$urlParams,'&raquo;', $pages['next']=='#'?'disabled':'');
              echo '</ul>';
            }else{
              echo 'Your stack are underflowing at the moment (aka there are no items present). Go ahead and an <a href="./">item</a>.';
            }
          ?>

          <table class="table table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>URL</th>
                <th>Title</th>
                <th>Tags</th>
                <th>Timestamp</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($items as $i) {
                $icon = "<img src=\"https://www.google.com/s2/favicons?domain={$i['url']} alt=\"favicon\"> ";
                $displayLink = (strlen($i['url'])>45)?substr($i['url'], 0,45).'...':$i['url'];
                $displayTitle = (strlen($i['title'])>40)?substr($i['title'], 0,35).'...':$i['title'];
                $displayTags = (strlen($i['tags'])>25)?substr($i['tags'], 0,25).'...':$i['tags'];

                $link = "<a href=\"{$i['url']}\" title=\"{$i['url']}\">$displayLink</a>";
                $titleTags = $displayTags != $i['tags']? "title=\"{$i['tags']}\"":'';
                $titleTitle = $displayTitle != $i['title']? "title=\"{$i['title']}\"":'';

                $timestamp = date('Y-m-d H:i ',strtotime($i['timestamp']));
                echo "<tr>".
                     "<td> {$i['id']}</td>".
                     "<td> $icon $link </td>".
                     "<td $titleTitle>$displayTitle</td>".
                     "<td $titleTags>$displayTags</td>".
                     "<td>{$timestamp}</td>".
                     "<td class=\"action\"><button type=\"button\" class=\"btn btn-default btn-sm delete-btn icon-button\" data-id=\"{$i['id']}\"><svg><use xlink:href=\"#icon-trash-o\" /></svg></button></td>".
                     "</tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <?php require_once TEMPLATES_PATH.'/footer.php'; ?>
    </body>
</html>
