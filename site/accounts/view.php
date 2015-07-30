<?php
set_include_path('..');

include_once('include/database.php');
include_once('include/api.php');

function preload_user() {
  global $user, $error_page;

  if (!array_key_exists('id',$_GET)) {
    http_response_code(404);
    $error_page = 'error_404_content';
  }

  $user = fetch_one_or_none('users', 'id', $_GET['id']);
  if (!$user) {
    http_response_code(404);
    $error_page = 'error_404_content';
  }
}

function content() {
  global $user;  
?>

  <h2><?php esc($user->name) ?></h2>

<?php
}

preload_user();
include('include/template.php');
