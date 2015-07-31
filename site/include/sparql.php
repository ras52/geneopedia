<?php
include_once('include/utils.php');

function send_sparql_any($sparql, $path, $param) {
  global $config;
  $out = curl_init( $config['sparql']['endpoint'].$path );

  curl_setopt($out, CURLOPT_POST, 1);
  curl_setopt($out, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($out, CURLOPT_POSTFIELDS, $param.'='.urlencode($sparql));
  $result = curl_exec($out) or die( curl_error($out) );
  curl_close($out);
  return $result;
}

function send_sparql_update($sparql) {
  send_sparql_any($sparql, 'update/', 'update');
}

function send_sparql_query($sparql) {
  return send_sparql_any($sparql, 'sparql/', 'query');
}

function tidy_rdf($rdf, $base, $mime) {
  $descriptorspec = array( 0 => array("pipe", "r"),
                           1 => array("pipe", "w"),
                           2 => array("file", "/tmp/log", "w") );

  if ($mime == 'application/rdf+xml') $fmt = 'rdfxml-abbrev';
  else if ($mime == 'application/n-triples') $fmt = 'ntriples';
  else if ($mime == 'text/turtle') $fmt = 'turtle';

  $cmd = "rapper -o $fmt - '$base'";
  $proc = proc_open( $cmd, $descriptorspec, $pipes );
  if (!$proc || !is_resource($proc)) return $rdf;

  fwrite( $pipes[0], $rdf ); fclose( $pipes[0] );

  $rdf2 = '';
  while (!feof($pipes[1])) { $rdf2 .= fgets($pipes[1], 4096); }
  fclose($pipes[1]);

  proc_close($proc);
  return $rdf2;
}




