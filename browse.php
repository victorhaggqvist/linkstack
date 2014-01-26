<?php
require_once 'core/config.inc';
require_once TEMPLATES_PATH.'head.php';

use Snilius\Util\PDOHelper;
use Snilius\Util\Bootstrap\Alert;
use Snilius\Util\Paginator;
use Snilius\StashItem;
use Snilius\StashManager;

$pdo = new PDOHelper($db_conf);
$stashManager = new StashManager($pdo);
$page = (isset($_GET['page']))?$_GET['page']:1;

?>
    <body>
    <?php
    require_once TEMPLATES_PATH.'header.php';
    if(!is_array($user))
      header('Location: ./');
    ?>

    <div class="container" style="margin-top: 20px;">
      <?php
      $stashManager->setUserid($user['id']);
      $resultset = array();
      $resultset['count'] = $stashManager->getCount();

      if (isset($_GET['q'])) {
        $q = $_GET['q'];
        $tags = isset($_GET['tags']);

        $query = $stashManager->queryItemsPage($q,$tags,$page);

        //$query = $stashManager->getItemsPage($page);
        $resultset['list'] = $query[2];

      }else {
        $query = $stashManager->getItemsPage($page);
        $resultset['list'] = $query[2];
      }
      ?>
      <form class="form-inline" role="form" action="browse.php" method="get">
        <div class="row">
          <div class="col-lg-6">
            <div class="input-group">
              <input type="search" class="form-control" id="q" placeholder="Search" name="q" value="<?php echo @$_GET['q']; ?>">
              <span class="input-group-btn">
                <button class="btn btn-default" type="submit">Search</button>
              </span>
            </div><!-- /input-group -->
          </div><!-- /.col-lg-6 -->
          <div class="col-lg-2">
            <div class="checkbox">
                <label style="margin-top: 7px; display: block;">
                  <input type="checkbox" name="tags" <?php echo (@$_GET['tags']=='on')?'checked="checked"':''; ?>> Only tags
                </label>
              </div>
          </div>
        </div>
      </form>
      <div class="row">
        <div class="col-lg-12">
          <?php
            $total = ceil($resultset['count']/30);
            $tpage = ($total>1)?'pages':'page';
            echo '<div style="display: block; margin-top: 2px;">Listing '.$resultset['count'].' links in '.$total.' '.$tpage.'</div>';

            $paging = new Paginator($resultset['count'],30);
            $pages = $paging->getPagination($page);

            function makeLink($href,$text,$class=''){
              $cls=($class=='')?'':'class="'.$class.'"';
              return '<li '.$cls.'><a href="'.$href.'">'.$text.'</a>';
            }

            echo '<ul class="pagination" style="margin: 4px 0 0 0;">';
            echo makeLink($pages['prev'],'&laquo;');
            foreach ($pages['nav'] as $key => $value) {
              if (($key+1)==$page)
                echo makeLink($value,$key+1,'active');
              else
                echo makeLink($value,$key+1);
            }
            echo makeLink($pages['next'],'&raquo;');
            echo '</ul>';

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
              foreach ($resultset['list'] as $i) {
                $icon = '<img src="https://www.google.com/s2/favicons?domain='.$i['url'].'" alt="favicon"> ';
                $displayLink = (strlen($i['url'])>55)?substr($i['url'], 0,55).'...':$i['url'];
                $displayTitle = (strlen($i['title'])>50)?substr($i['title'], 0,50).'...':$i['title'];
                $link = '<a href="'.$i['url'].'" title="'.$i['url'].'">'.$displayLink.'</a>';
                echo '<tr>'.
                     '<td>'.$i['id'].'</td>'.
                     '<td>'.$icon.$link.'</td>'.
                     '<td title="'.$i['title'].'">'.$displayTitle.'</td>'.
                     '<td>'.$i['tags'].'</td>'.
                     '<td>'.date('Y-m-d H:i ',strtotime($i['timestamp'])).'</td>'.
                     '<td class="action"><button type="button" class="btn btn-default btn-sm delete-btn" data-id="'.$i['id'].'"><span class="glyphicon glyphicon-trash"></span></button></td>'.
                     '</tr>';
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <?php require_once TEMPLATES_PATH.'footer.php'; ?>
    </body>
</html>
