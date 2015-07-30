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
  if (!$user || is_null($user->date_verified)) {
    http_response_code(404);
    $error_page = 'error_404_content';
  }
}

function header_content() { 
  global $user; ?>
  <link rel="alternate" type="application/rdf+xml" href="<?php 
    esc($user->id) ?>.rdf" />
<?php }


function content() {
  global $user;  
  page_header($user->name);
}

preload_user();

# RDF content negotiation
$accept = new http\Header('Accept', $_SERVER['HTTP_ACCEPT']);
$ct = $accept->negotiate( array('text/html','application/rdf+xml') ); 
if ($ct == 'application/rdf+xml')
  return do_redirect('accounts/'.$user->id.'.rdf');

include('include/template.php');
