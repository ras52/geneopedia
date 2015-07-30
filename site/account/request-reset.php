<?php
set_include_path('..');

include_once('include/database.php');
include_once('include/forms.php');
include_once('include/activation.php');

function send_reset_email($email, $name, $token) {
  global $config;

  error_log("Sending reset token '$token' to <$email>");

  $root = 'http://'.$config['domain'].$config['http_path'];

  $msg  = "Someone, hopefully you, has requested a reset of the password for \n"
        . $config['title']." account.  To reset it please visit:\n"
        . "\n"
        . "  ${root}account/reset-password/".$token."\n"
        . "\n"
        . "If you did not request this reset, there is no need to \n"
        . "take any further action, and you will not receive further \n"
        . "mail from us.\n";

  mail( sprintf('"%s" <%s>', $name, $email),
        $config['title']." password reset", $msg )
    or die('Unable to send email');
}

function content() { 
  $errors = array();
  ?>
      <h2>Request password reset</h2>
  <?php

  if (array_key_exists('reset',$_POST)) {
    if (!isset($_POST['email']) || !$_POST['email'])
      $errors[] = "Please enter an email address";
    else {
      $user = fetch_one_or_none('users', 'email_address', $_POST['email']);

      if (!$user)
        $errors[] = "Incorrect email address supplied";

      if (count($errors) == 0) {
        $token = make_random_token();
        update_all('users', array('activation_token' => $token),
                   'id', $user->id ); 
        send_reset_email($user->email_address, $user->name, $token); ?>
        <p>We have sent you an email containing a link allowing you to reset 
          your password.</p>
        <?php return;
      }
    }
  } ?>
    <p>If you have forgotten your password and need it resetting, please 
      enter your email address below and we will send you an email allowing 
      you to reset your password.</p>

    <?php show_error_list($errors); ?>
 
    <form method="post" action="" accept-charset="UTF-8">
      <div class="fieldrow">
        <?php text_field($_POST, 'email', 'Email address') ?>
      </div>

      <div class="fieldrow">
        <input type="submit" name="reset" value="Reset" />
      </div>
    </form>
<?php }

include('include/template.php');
