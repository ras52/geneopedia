<?php
set_include_path('..');

include_once('include/database.php');
include_once('include/sparql.php');

function error() {
  http_response_code(404);
  header('Content-Type: text/plain');
  exit;
}

function retrieve_rdf($user, $mime) {
  global $config;
  $uri = "http://".$config['domain'].$config['http_path'].'accounts/'
       . $user->id;

  $sparql = <<<EOF
CONSTRUCT { ?s ?p ?o } 
WHERE { 
  GRAPH <$uri.rdf> { ?s ?p ?o } . 
}
EOF;

  $rdf = send_sparql_query($sparql);
  return tidy_rdf($rdf, "$uri.rdf", $mime);
}

function raw_content() {
  global $config;

  if (!array_key_exists('id',$_GET)) error();

  $user = fetch_one_or_none('users', 'id', $_GET['id']);
  if (!$user || is_null($user->date_verified)) error();

  $mime = 'application/rdf+xml';
  if (array_key_exists('extension', $_GET)) {
    $ext = $_GET['extension'];
    if ($ext == 'ttl' || $ext == 'turtle') $mime = 'text/turtle';
    else if ($ext == 'nt' || $ext == 'n3') $mime = 'application/n-triples';
  }

  header("Content-Type: $mime");
  echo retrieve_rdf($user, $mime);
}

raw_content();
