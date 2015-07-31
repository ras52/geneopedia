<?php
set_include_path('..');

include_once('include/database.php');
include_once('include/activation.php');

function send_approval_request($user, $admins) {
  global $config;
  $root = 'http://'.$config['domain'].$config['http_path'];

  $msg = "The following user has requested an account on "
         . $config['title'].".\n"
       . "\n"
       . "  ".$user->name." <".$user->email_address.">\n"
       . "\n"
       . "To approve this account, please follow the following link:\n"
       . "\n"
       . "  ${root}account/approve/".$user->id."\n"
       . "\n";

  foreach ($admins as $a) {
    mail( sprintf('"%s" <%s>', $a->name, $a->email_address),
          $config['title']." account approval", $msg )
      or die('Unable to send email');
  }

}

function content() {
  $errors = array();

  if (!array_key_exists('token', $_GET) || !$_GET['token'])
    $errors[] = 'Invalid activation token';

  $token = $_GET['token'];

  $user = fetch_one_or_none('users', 'activation_token', $_GET['token']);
  if (!$user)
    $errors[] = 'Invalid activation token';
 
  if (count($errors)) { 
    page_header('Activation failed');
    show_error_list($errors);
    return;
  }

  $admins = fetch_wol('*', 'users', 
    'date_verified IS NOT NULL AND date_approved IS NOT NULL',
    'id ASC'); 

  $sets = array('activation_token' => null,
                'date_verified' => date('Y-m-d H:i:s'));

  # Auto-approve user 1.
  if (count($admins) == 0) {
    $sets['date_approved'] = $sets['date_verified'];
    $sets['approved_by']   = 1;
  }
 
  update_all('users', $sets, 'id', $user->id ); 

  page_header('Account activated');

  if (count($admins)) {
    send_approval_request($user, $admins);  ?>

    <p>Thank you for activating your account.
      Your request for an account has been forwarded to a site administrator
      for approval.  You will be notified by email when it is approved.</p>

  <?php } else {
    register_user_rdf($user);

    # Don't set login cookie now.  This is to prevent someone hijacking
    # a login token, using it, and benefiting from a pre-logged-in session.  
    # This way, they still need a password.

    global $config; ?>

    <p>Thank you for activating your account.
      You shouldn't need to do that again.  You may now want to 
      <a href="<?php esc($config['http_path']) ?>account/login">log in</a>.</p>

  <?php }
}

include('include/template.php');
