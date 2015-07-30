<?php

function validate_email_address($email)
{
   # Based on http://www.linuxjournal.com/article/9585

   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         // local part length exceeded
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         // domain part length exceeded
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         // local part starts or ends with '.'
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         // local part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         // character not valid in domain part
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         // domain part has two consecutive dots
         $isValid = false;
      }
      else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         // character not valid in local part unless 
         // local part is quoted
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
         // domain not found in DNS
         $isValid = false;
      }
   }
   return $isValid;
}

function make_random_token() {
  return sha1(openssl_random_pseudo_bytes(16));
}

function send_activation_email($email, $name, $token) {
  global $config;

  error_log("Sending activation token '$token' to <$email> (uid: $uid)");

  $root = 'http://'.$config['domain'].$config['http_path'];

  $msg  = "Thank you for registering with ".$config['title']."\n"
        . "\n"
        . "To activate your account, please follow the following link:\n"
        . "\n"
        . "  ${root}activate.php?token=".$token."\n"
        . "\n"
        . "If you did not request this account, there is no need to \n"
        . "take any further action, and you will not receive further \n"
        . "mail from us.\n";

  mail( sprintf('"%s" <%s>', $name, $email),
        $config['title']." account activation", $msg )
    or die('Unable to send email');
}

