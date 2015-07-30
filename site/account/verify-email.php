<?php
set_include_path('..');

include_once('include/database.php');
include_once('include/forms.php');
include_once('include/perms.php');

function content() {
  if (!user_logged_in()) return must_log_in();

  $user = fetch_one_or_none('users', 'id', user_logged_in());

  if (!array_key_exists('token', $_GET) || !$_GET['token']
      || $_GET['token'] != sha1($user->new_email_address))
    $errors[] = 'Invalid reset token';

  # This can happen if two accounts try to change address at similar times.  
  if (count($errors) == 0 && 
      count(fetch_all('users', 'email_address', $user->new_email_address)))
    $errors[] = "A user with this email address already exists";

  if (count($errors) == 0) {
    update_all('users', array('email_address' => $user->new_email_address,
                              'new_email_address' => null),
               'id', user_logged_in() ); ?>
    <h2>Address changed</h2>
    <p>Your email address has been changed to
      <tt><?php esc($user->new_email_address) ?></tt>.</p>
    <?php return;
  } 
  page_header('Address verification failed');
  show_error_list($errors); 
}

include('include/template.php');

