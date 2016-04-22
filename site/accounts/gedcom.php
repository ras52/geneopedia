<?php
set_include_path('..');

include_once('include/database.php');

function raw_content() {
  if (!array_key_exists('id',$_GET)) {
    http_response_code(404);
    $error_page = 'error_404_content';
  }

  $user = fetch_one_or_none('users', 'id', $_GET['id']);
  if (!$user || is_null($user->date_verified)) {
    http_response_code(404);
    $error_page = 'error_404_content';
  }

  global $config;
  $title = $config['title'];
  $uid = $user->id;
  $uname = $user->name;

  $gedcom = <<<EOF
0 HEAD
1 SOUR $title
1 SUBM @U$uid@
1 GEDC 
2 VERS 5.5
2 FORM LINEAGE-LINKED
1 CHAR UTF-8
0 @U$uid@ SUBM
1 NAME $uname
0 TRLR

EOF;

  header('Content-Type: application/x-gedcom');
  echo $gedcom;
}

raw_content();

global $error_page;
if ($error_page) 
  include('include/template.php');
