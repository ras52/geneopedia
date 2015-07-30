<?php
include_once('include/database.php');
include_once('include/forms.php');
include_once('include/activation.php');


function content() {
  db_connect();

  $errors = array();
  if (array_key_exists('register', $_POST)) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if (!$name || !$email || !$password || !$password2) {
      $errors[] = "Please fill in all the fields";
    }
    if ($password && $password2 && $password != $password2) {
      $errors[] = "Passwords do not match";
      $_POST['password'] = ''; $_POST['password2'] = '';
    }
    if ($email && !validate_email_address($email)) {
      error_log("Invalid email address <$email> while registering");
      $errors[] = "Invalid email address";
    }
    if (count($errors) == 0 && 
        count(fetch_all('users', 'email_address', $email))) {
      $errors[] = "A user with this email address already exists";
    }

    if (count($errors) == 0) {
      $token = make_random_token();
      $data = array( 
        'name'             => $name,
        'email_address'    => $email,
        'password_crypt'   => crypt($password),
        'date_registered'  => date('Y-m-d H:i:s'),
        'activation_token' => $token
      );
      insert_array_contents('users', $data);
      send_activation_email($email, $name, $token); ?>

      <h2>Account registered</h2>

      <p>An email has just been sent to the email address you supplied.  This
        contains a link which you should follow to activate your account.</p>
      
      <?php
    }
  }
  ?>
    <h2>Register for an account</h2>
    <?php show_error_list($errors); ?>

    <form method="post" action="" accept-charset="UTF-8">
      <div class="fieldrow">
        <?php text_field($_POST, 'name', 'Name', 'publicly visible') ?>
      </div>

      <div class="fieldrow">
        <?php text_field($_POST, 'email', 'Email address') ?>
      </div>

      <div class="fieldrow">
        <div>
          <label for="password">Password</label>
          <input type="password" id="password" name="password" 
            value="<?php esc($_POST['password']) ?>" />
        </div>
        <div>
          <label for="password2">Confirm password</label>
          <input type="password" id="password2" name="password2" 
            value="<?php esc($_POST['password2']) ?>" />
        </div>
      </div>

      <div class="fieldrow">
        <input type="submit" name="register" value="Register" />
      </div>
    </form>
  <?php
}

include('include/template.php');
