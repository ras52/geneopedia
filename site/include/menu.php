<?php 
include_once('include/perms.php');

function menu() {
  global $config;
  $root = $config['http_path'];
?>
  <ul>
    <li><a href="<?php esc($root) ?>">Home</a></li>
    <?php if (user_logged_in()) { ?>
    <li><a href="<?php esc($root) ?>files">Files</a></li>
    <li><a href="<?php esc($root) ?>account">Account</a></li>
    <li><a href="<?php esc($root) ?>account/logout">Log out</a></li>
    <?php } else { ?>
    <li><a href="<?php esc($root) ?>account/register">Register</a></li>
    <li><a href="<?php esc($root) ?>account/login">Log in</a></li>
    <?php } ?>
  </ul>
<?php }

menu();
