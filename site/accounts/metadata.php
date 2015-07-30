<?php
set_include_path('..');

include_once('include/database.php');

function error() {
  http_response_code(404);
  header('Content-Type: text/plain');
  exit;
}

function raw_content() {
  global $config;

  if (!array_key_exists('id',$_GET)) error();

  $user = fetch_one_or_none('users', 'id', $_GET['id']);
  if (!$user || is_null($user->date_verified)) error();

  header('Content-Type: application/rdf+xml');

  $base = "http://" . $config['domain'] . $_SERVER['REQUEST_URI'];

?><?xml version="1.0" encoding="UTF-8"?>
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns:dcterms="http://purl.org/dc/terms/"
         xmlns:wdrs="http://www.w3.org/2007/05/powder-s#"
         xml:base="<?php esc($base) ?>">
  <dcterms:Agent rdf:ID="A">
    <wdrs:describedby rdf:resource="<?php esc($user->id) ?>.rdf"/>
  </dcterms:Agent>
</rdf:RDF>
<?php }

raw_content();
