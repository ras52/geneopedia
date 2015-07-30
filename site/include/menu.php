<?php 
include_once('include/perms.php');
?>
<ul>
  <li><a href=".">Home</a></li>
  <?php if (user_logged_in()) { ?>
  <li><a href="logout.php">Log out</a></li>
  <?php } else { ?>
  <li><a href="register.php">Register</a></li>
  <li><a href="login.php">Log in</a></li>
  <?php } ?>
</ul>

