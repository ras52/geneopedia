<?php
set_include_path('..');

include_once('include/database.php');

function content() {
  $users = fetch_wol('*', 'users', 
                     'date_verified IS NOT NULL AND date_approved IS NOT NULL',
                     'name ASC'); ?>
  <h2>Accounts</h2>

  <table>
    <?php foreach ($users as $u) { ?>
    <tr>
      <td class="name"><a href="<?php esc($u->id) ?>"><?php 
        esc($u->name) ?></a></td>
    </tr>
    <?php } ?>
  </table>
<?php }

include('include/template.php');
