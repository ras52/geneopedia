<?php
set_include_path('..');

include_once('include/database.php');
include_once('include/forms.php');
include_once('include/activation.php');

function send_email_change_email($email, $name) {
  global $config;

  $token = sha1($email);

  error_log("Sending email address verification to <$email>");

  $root = 'http://'.$config['domain'].$config['http_path'];

  $msg  = "You have requested to associate this email address with your "
        . $config['title']." \naccount.  To activate this change, please "
        . "follow this link:\n"
        . "\n"
        . "  ${root}account/verify-email/".$token."\n";

  mail( sprintf('"%s" <%s>', $name, $email),
        $config['title']." address verification", $msg )
    or die('Unable to send email');
}

function content() { 
  if (!user_logged_in()) return must_log_in();

  $user = fetch_one_or_none('users', 'id', user_logged_in());

  $errors = array();

  if (array_key_exists('change',$_POST)) {
    if (!isset($_POST['email']) || !$_POST['email'])
      $errors[] = "Please enter an email address";
    else {
      $email = $_POST['email'];
      if ($email && !validate_email_address($email))
        $errors[] = "Invalid email address";
      if (count($errors) == 0 && 
          count(fetch_all('users', 'email_address', $email)))
        $errors[] = "A user with this email address already exists";

      if (count($errors) == 0) {
        update_all('users', array('new_email_address' => $email),
                   'id', user_logged_in() ); 
        send_email_change_email($email, $user->name); ?>
        <p>We have sent an email to your new address requesting that you
          confirm that change of address.</p>
        <?php return;
      }      
    }
  }

  $fields = array();

  page_header('Change email address');
  show_error_list($errors); ?>
 
    <form method="post" action="" accept-charset="UTF-8">
      <div class="fieldrow">
        <div class="field">
          <label>Current address:</label>
          <div><tt><?php esc($user->email_address) ?></tt></div>
        </div>
      </div>

      <div class="fieldrow">
        <?php text_field($fields, 'email',  'New address') ?>
      </div>

      <div class="fieldrow">
        <input type="submit" name="change" value="Change"/>
      </div>
    </form>
  <?php

}

include('include/template.php');
