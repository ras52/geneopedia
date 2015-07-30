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
