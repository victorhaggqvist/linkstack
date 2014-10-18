<h3>Recently Pushed</h3>
<table class="table table-striped table-hover" id="recentlist">
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
    $sm = new \Snilius\Stack\StackManager($user);
    $items = $sm->getItems(null, 1, 15);

    foreach ($items as $item) {
      $icon = '<img src="https://www.google.com/s2/favicons?domain='.$item['url'].'" alt="favicon"> ';
      $displayLink = (strlen($item['url'])>55)?substr($item['url'], 0,55).'...':$item['url'];
      $displayTitle = (strlen($item['title'])>50)?substr($item['title'], 0,50).'...':$item['title'];
      $displayTags = (strlen($item['tags'])>25)?substr($item['tags'], 0,25).'...':$item['tags'];
      $link = '<a href="'.$item['url'].'" title="'.$item['url'].'">'.$displayLink.'</a>';

      $titleTitle = $displayTitle != $item['title']? "title=\"{$item['title']}\"":'';
      $titleTags = $displayTags != $item['tags']? "title=\"{$item['tags']}\"":'';

      $date = date('Y-m-d H:i', strtotime($item['timestamp']));
      echo "<tr>".
           "<td> $icon $link </td>".
           "<td $titleTitle>$displayTitle</td>".
           "<td $titleTags>$displayTags</td>".
           "<td>$date</td>".
           '<td class="action"><button type="button" class="btn btn-default btn-sm delete-btn icon-button" data-id="'.$item['id'].'"><svg><use xlink:href="#icon-trash-o" /></svg></button></td>'.
           '</tr>';
    }
    ?>
  </tbody>
</table>
