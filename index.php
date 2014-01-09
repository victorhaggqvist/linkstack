<?php
require_once 'head.php'; 

use Snilius\Util\PDOHelper;
use Snilius\StashItem;
use Snilius\Util\Bootstrap\Alert;
use Snilius\StashManager;

$pdo = new PDOHelper($db_conf);

?>
    <body>
    <header>
      <div class="container">
        <h1>Linkstash</h1>
        <ul class="nav nav-pills">
          <li class="active"><a href="index.php">Stash Home</a></li>
          <li><a href="browse.php">Browse Stash</a></li>
        </ul>
      </div>
    </header>
    
    <div class="container">
      <div class="row">
        <div class="col-lg-8">
          <h2>Stash new item</h2>
          <?php 
          if (isset($_POST['stash'])) {
            $url = $_POST['url'];
            $title = $_POST['title'];
            $tags = $_POST['tags'];
            
            $s = new StashItem($pdo);
            if($s->newItem($url, $title, $tags))
              echo Alert::success("URL Stashed!");
          }
          ?>
          <form class="form-horizontal" role="form" action="index.php" method="post">
            <div class="form-group">
              <label for="url" class="col-sm-1 control-label">Url</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="url" name="url" placeholder="http://example.com">
              </div>
            </div>
            <div class="form-group">
              <label for="title" class="col-sm-1 control-label">Title</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="title" name="title" placeholder="Example">
              </div>
            </div>
            <div class="form-group">
              <label for="tags" class="col-sm-1 control-label">Tags</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="tags" name="tags" placeholder="Tag One, Tag Two, Tag N">
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-offset-1 col-sm-10">
                <button type="submit" class="btn btn-primary btn-block" name="stash">Stash</button>
              </div>
            </div>
          </form>
        </div>
        <div class="col-lg-4">
          <h2>Recently Stashed</h2>
          <div>
          <?php 
          $stashManager = new StashManager($pdo);
          $list = $stashManager->getItems(10);
          foreach ($list as $i) {
            echo '<img src="https://www.google.com/s2/favicons?domain='.$i['url'].'" alt="fav">'.
                 '<strong><a href="'.$i['url'].'">'.$i['title'].'</a><strong> ('.date('Y-m-d',strtotime($i['timestamp'])).')<br>'.
                 '- <a href="'.$i['url'].'">'.$i['url'].'</a>';
          }
          ?>
          </div>
        </div>
      </div>
    </div>
    
    <?php require_once 'footer.php'; ?>
    </body>
</html>
