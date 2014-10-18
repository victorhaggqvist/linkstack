<?php
use Snilius\StashItem;
use Snilius\StashManager;
?>
<h2>Push new</h2>
<div class="row">
  <div class="col-sm-offset-1 col-sm-10">
    <div id="notify" class="alert">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <span class="bold">Sweet!</span>
      <span id="notify-text"></span>
    </div>
  </div>
</div>

<form class="form-horizontal" role="form" id="push-form">
  <div class="form-group">
    <label for="url" class="col-sm-1 control-label">Url</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="url" name="url" placeholder="http://example.com" autofocus="autofocus" autocomplete="off">
    </div>
  </div>
  <div class="form-group">
    <label for="title" class="col-sm-1 control-label">Title</label>
    <div class="col-sm-10">
      <input type="text" class="form-control bgspin" id="title" name="title" placeholder="Example" autocomplete="off">
    </div>
  </div>
  <div class="form-group">
    <label for="tags" class="col-sm-1 control-label">Tags</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="tags" name="tags" placeholder="Tag One, Tag Two, Tag N" autocomplete="off">
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-1 col-sm-10">
      <button class="btn btn-primary btn-block" id="push-it" name="push">Push It!</button>
    </div>
  </div>
</form>
