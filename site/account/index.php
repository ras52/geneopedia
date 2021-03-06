<?php
set_include_path('..');

include_once('include/database.php');
include_once('include/forms.php');
include_once('include/perms.php');

function content() {
  if (!user_logged_in()) return must_log_in();

  $user = fetch_one_or_none('users', 'id', user_logged_in());

  page_header('Account');
  $errors = array();

  if (array_key_exists('apply',$_POST)) {
    if (!isset($_POST['name']) || !$_POST['name'])
      $errors[] = "Please provide a name";

    if (count($errors) == 0) {
      $sets = array('name' => $_POST['name']);
      update_all('users', $sets, 'id', $user->id);
      update_local_object($user, $sets); ?>
      <p>Your changes have been applied.  
        Return to <a href=".">account</a> page.</p> 
      <?php return;
    }
    show_error_list($errors);
  }

  $fields = array( 'name' => $user->name,
                   'email' => $user->email_address );

  ?>

    <form method="post" action="" accept-charset="UTF-8">
      <fieldset>
        <legend>Details</legend>
        <div class="fieldrow">
          <?php text_field($fields, 'name', 'Name', 'publicly visible') ?>
        </div>
        <div class="fieldrow">
          <div class="field">
            <label>Email address</label>
            <div><tt><?php esc($fields['email']) ?></tt>
            <a class="control small" style="padding-left: 1em" 
               href="change-email">Change</a></div>
          </div>
        </div>
        <div class="fieldrow">
          <div class="field">
            <label>Password</label>
            <div><tt>********</tt>
            <a class="control small" style="padding-left: 1em" 
               href="reset-password">Change</a></div>
          </div>
        </div>
        <div class="fieldrow">
          <input type="submit" name="apply" value="Update"/>
        </div>
      </fieldset>

      <fieldset>
        <legend>Contact details</legend>
        <p>Any details entered here will be made publicly available.</p>
<?php /*NAME, ADDR, PHON, EMAIL, FAX, WWW, OBJE, LANG, RFN, RIN, NOTE, CHAN*/?>
      </fieldset>
    </form>
<?php
}

include('include/template.php');
