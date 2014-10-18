<?php
use Snilius\Util\Gravatar;

?>
<svg style="display:none;"><symbol viewBox="0 0 2048 2048" id="icon-spinner"><title>spinner</title><path d="M736 1472q0 60-42.5 102t-101.5 42q-60 0-102-42t-42-102 42-102 102-42q59 0 101.5 42t42.5 102zm432 192q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm-608-640q0 66-47 113t-113 47-113-47-47-113 47-113 113-47 113 47 47 113zm1040 448q0 46-33 79t-79 33-79-33-33-79 33-79 79-33 79 33 33 79zm-832-896q0 73-51.5 124.5t-124.5 51.5-124.5-51.5-51.5-124.5 51.5-124.5 124.5-51.5 124.5 51.5 51.5 124.5zm464-192q0 80-56 136t-136 56-136-56-56-136 56-136 136-56 136 56 56 136zm544 640q0 40-28 68t-68 28-68-28-28-68 28-68 68-28 68 28 28 68zm-208-448q0 33-23.5 56.5t-56.5 23.5-56.5-23.5-23.5-56.5 23.5-56.5 56.5-23.5 56.5 23.5 23.5 56.5z"/></symbol><symbol viewBox="0 0 2048 2048" id="icon-trash-o"><title>trash-o</title><path d="M832 864v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm256 0v576q0 14-9 23t-23 9h-64q-14 0-23-9t-9-23v-576q0-14 9-23t23-9h64q14 0 23 9t9 23zm128 724v-948h-896v948q0 22 7 40.5t14.5 27 10.5 8.5h832q3 0 10.5-8.5t14.5-27 7-40.5zm-672-1076h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></symbol><symbol viewBox="0 0 2048 2048" id="icon-trash"><title>trash</title><path d="M832 1504v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm256 0v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm256 0v-704q0-14-9-23t-23-9h-64q-14 0-23 9t-9 23v704q0 14 9 23t23 9h64q14 0 23-9t9-23zm-544-992h448l-48-117q-7-9-17-11h-317q-10 2-17 11zm928 32v64q0 14-9 23t-23 9h-96v948q0 83-47 143.5t-113 60.5h-832q-66 0-113-58.5t-47-141.5v-952h-96q-14 0-23-9t-9-23v-64q0-14 9-23t23-9h309l70-167q15-37 54-63t79-26h320q40 0 79 26t54 63l70 167h309q14 0 23 9t9 23z"/></symbol></svg>
<header>
  <div class="container">
    <h1>Link Stack</h1>

    <ul class="nav nav-pills">
    <?php
    if (is_object($user)) { //if logged in
      ?>
      <li <?php echo (strpos($_SERVER['PHP_SELF'],"index")!==false)?'class="active"':"";?>><a href="./">Stack Home</a></li>
      <li <?php echo (strpos($_SERVER['PHP_SELF'],"browse")!==false)?'class="active"':""; ?>><a href="browse.php">Browse Stack</a></li>
      <li class="pull-right">
        <?php
        echo "<strong>{$user->getName()}</strong>";
//        echo "<strong>{$user->getEmail()}</strong>";
        $img = Gravatar::getAvatar($user->getEmail(),40);
        echo " <img src=\"$img\" alt=\"avatar\" class=\"img-rounded\"/>";
        echo '<a href="logout.php" style="display: inline-block; margin-left: 5px;" class="btn btn-default" role="button">Sign out</a>';
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
