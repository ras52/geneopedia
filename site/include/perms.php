<?php
include_once('include/utils.php');

function user_logged_in() {
  if (!isset($_COOKIE['uid']) || !isset($_COOKIE['auth']))
    return null;

  $uid = $_COOKIE['uid'];
  $auth = $_COOKIE['auth'];

  global $config;
  $secret = $config['auth']['secret'];
  if ($uid && $auth && $secret
      && crypt($uid . $secret, $auth) == $auth)
    return $uid;

  return null;
}

function must_log_in() {
  global $config;
  $root = $config['http_path'];
  $ret_url = preg_replace("#^\Q$root\E#", '', $_SERVER['REQUEST_URI'] );

 ?>
    <h2>Please log in</h2>

    <p>You must <a href="<?php esc($root) ?>account/login?return=<?php 
      esc(urlencode($ret_url)) ?>">log in</a> before you can do this.  
      If you do not have an account, you may want to
      <a href="<?php esc($root) ?>account/register">register</a> for one.</p>

<?php }
