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
  global $user; 

  $types = array( 'rdf' => 'application/rdf+xml',
                  'ttl' => 'text/turtle',
                  'nt'  => 'application/n-triples',
                  'ged' => 'application/x-gedcom' );
  foreach ( array_keys($types) as $ext ) { ?>
    <link rel="alternate" type="<?php esc($types[$ext]) ?>" href="<?php 
      esc($user->id) ?>.<?php esc($ext) ?>" />
  <?php }
}


function content() {
  global $user;  
  page_header($user->name);
}

preload_user();

# RDF content negotiation
$accept = new http\Header('Accept', $_SERVER['HTTP_ACCEPT']);
$ct = $accept->negotiate( array('text/html','application/rdf+xml', 
                                'application/x-gedcom') ); 
if ($ct == 'application/rdf+xml')
  return do_redirect('accounts/'.$user->id.'.rdf');
else if ($ct == 'application/x-gedcom')
  return do_redirect('accounts/'.$user->id.'.ged');

include('include/template.php');
