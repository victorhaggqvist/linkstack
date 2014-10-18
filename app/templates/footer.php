<div class="container">
  <hr>
  <footer style="margin-bottom: 40px;">
    <div class="row">
      <div class="col-sm-4">
        &copy; Snilius 2013 - 2014
      </div>
      <div class="col-sm-4">
        Licensed under GPLv2
      </div>
      <div class="col-sm-4">
        <a href="https://github.com/victorhaggqvist/linkstack">Source at GitHub</a>
      </div>
    </div>
  </footer>
</div> <!-- /.container -->

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/jquery.min.js"><\/script>')</script>
<script src="js/bootstrap.min.js"></script>
<script src="js/stack.js"></script>

<?php

if (is_object($user)){
  $cred = $user->getApiCredentials();
  echo "<script>LinkStack.setUp('{$cred['key']}', '{$cred['timestamp']}');</script>";
}

?>

