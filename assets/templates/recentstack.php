<?php
use Snilius\StashManager;
?>
<h3>Recently Pushed</h3>
<table class="table table-striped" id="recentlist">
  <thead>
    <tr>
      <th>URL</th>
      <th>Title</th>
      <th>Tags</th>
      <th>Timestamp</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php
    $stashManager = new StashManager($pdo);
    $stashManager->setUserid($user['id']);
    $list = $stashManager->getItems(10)[2];

    foreach ($list as $i) {
      $icon = '<img src="https://www.google.com/s2/favicons?domain='.$i['url'].'" alt="favicon"> ';
      $displayLink = (strlen($i['url'])>55)?substr($i['url'], 0,55).'...':$i['url'];
      $displayTitle = (strlen($i['title'])>50)?substr($i['title'], 0,50).'...':$i['title'];
      $link = '<a href="'.$i['url'].'" title="'.$i['url'].'">'.$displayLink.'</a>';
      echo '<tr>'.
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
