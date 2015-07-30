<?php
set_include_path('..');

include_once('include/database.php');

function content() {
  $errors = array();

  if (!array_key_exists('token', $_GET) || !$_GET['token'])
    $errors[] = 'Invalid activation token';

  $token = $_GET['token'];

  $user = fetch_one_or_none('users', 'activation_token', $_GET['token']);
  if (count($user) != 1)
    $errors[] = 'Invalid activation token';
 
  if (count($errors)) { 
    page_header('Activation failed');
    show_error_list($errors);
    return;
  }


  update_all('users', array('activation_token' => null,
                            'date_verified' => date('Y-m-d H:i:s')), 
             'id', $user->id ); 

  # Don't set login cookie now.  This is to prevent someone hijacking
  # a login token, using it, and benefiting from a pre-logged-in session.  
  # This way, they still need a password.

  global $config;
  ?>

    <h2>Account activated</h2>

    <p>Thank you for activating your account.
      You shouldn't need to do that again.  You may now want to 
      <a href="<?php esc($config['http_path']) ?>account/login">log in</a>.</p>

<?php }

include('include/template.php');
