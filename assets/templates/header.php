<?php
use Snilius\Util\Gravatar;

?>
<header>
  <div class="container">
    <h1>Link Stack</h1>

    <ul class="nav nav-pills">
    <?php
    if (is_array($user)) { //if logged in
      ?>
      <li <?php echo (strpos($_SERVER['PHP_SELF'],"index")!==false)?'class="active"':"";?>><a href="index.php">Stack Home</a></li>
      <li <?php echo (strpos($_SERVER['PHP_SELF'],"browse")!==false)?'class="active"':""; ?>><a href="browse.php">Browse Stack</a></li>
      <li class="pull-right">
        <?php
        echo 'Signed in as <strong>'.$user['email'].'</strong> <img src="'.Gravatar::getAvatar($user['email'],40).' alt="avatar" />'.
             '<a href="logout.php" style="display: inline-block; margin-left: 5px;" class="btn btn-default" role="button">Sign out</a>';
        ?>
      </li>
      <?php
    }else{ //not logged in
      ?>
      <li<?php echo (strpos($_SERVER['PHP_SELF'],"index")!==false)?' class="active"':"";?>><a href="index.php">Stack Home</a></li>
      <li class="pull-right"><a href="login.php" class="btn btn-danger" role="button">Login with Google</a></li>
      <?php
    }
    ?>
    </ul>
  </div>
</header>
