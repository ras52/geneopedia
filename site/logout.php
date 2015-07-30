<?php

include('include/utils.php');

function logout() {
  unset($_COOKIE['uid']); setcookie('uid', NULL, -1);
  unset($_COOKIE['auth']); setcookie('auth', NULL, -1);
}

logout();
do_redirect('index.php');

