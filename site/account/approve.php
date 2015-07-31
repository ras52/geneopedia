<?php
set_include_path('..');

include_once('include/database.php');
include_once('include/forms.php');

function content() {
  global $config;
  if (!user_logged_in()) return must_log_in();

  $errors = array();

  if (!array_key_exists('id',$_GET))
    $errors[] = 'No user ID';

  if (count($errors) == 0) {
    $user = fetch_one_or_none('users', 'id', $_GET['id']);
    if (!$user)
      $errors[] = 'No such user';
    if (!$user->date_verified)
      $errors[] = 'User has not yet been verified';
    if ($user->date_approved)
      $errors[] = 'User has already been approved';
  }

  if (count($errors)) {
    page_header("Error approving account");
    show_error_list($errors);
    return;
  }

  if (!$user->date_approved)
    update_all( 'users', array(
      'date_approved' => date('Y-m-d H:i:s'),
      'approved_by' => user_logged_in()
    ), 'id', $user->id );

  $root = 'http://'.$config['domain'].$config['http_path'];

  $msg = "Your ".$config['title']." account has been approved.  "
           . "To log in, please follow \n"
       . "the following link:\n"
       . "\n"
       . "  ${root}account/login\n" 
       . "\n";

  mail( sprintf('"%s" <%s>', $user->name, $user->email_address),
        $config['title']." account approved", $msg )
    or die('Unable to send email');
  
  page_header("Account approved"); ?>

  <p>Thank you for approving <?php esc($user->name) ?>'s account.</p>

<?php }

include('include/template.php');

