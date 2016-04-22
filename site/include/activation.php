<?php
include_once('include/sparql.php');

function validate_email_address($email) {
   # Based on http://www.linuxjournal.com/article/9585

   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex) {
      return false;
   }
   else {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);

      if ($localLen < 1 || $localLen > 64)
         return false;
      
      else if ($domainLen < 1 || $domainLen > 255)
         return false;
      
      // Local part may not starts or ends with a dot
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
         return false;
      
      // Local part may not contain two consecutive dots
      else if (preg_match('/\\.\\./', $local))
         return false;
     
      // Valid characters in domain are A-Z, a-z, 0-9, - and .
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
         return false;
      
      // Domain has two consecutive dots
      else if (preg_match('/\\.\\./', $domain))
        return false;
      
      else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local))) {
         // XXX.  Not sure about this test.
         // Apparently "character not valid in local part unless 
         // local part is quoted"
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
           return false;
      }
      // Is the domain in DNS?
      if (!checkdnsrr($domain,"MX") && !checkdnsrr($domain,"A"))
         return false;
   }
   return true;
}

function make_random_token() {
  return sha1(openssl_random_pseudo_bytes(16));
}

function register_user_rdf($user) {
  global $config;
  $uri = "http://".$config['domain'].$config['http_path'].'users/'
       . $user->id;

  $update = <<<EOF
PREFIX dcterms: <http://purl.org/dc/terms/>
PREFIX wdrs: <http://www.w3.org/2007/05/powder-s#>
INSERT DATA {
  GRAPH <$uri.rdf> {
    <$uri> a dcterms:Agent ; 
           wdrs:describedby <$uri.rdf> .
  }
}
EOF;
  send_sparql_update($update);
}
