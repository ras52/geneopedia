<?php
set_include_path('..');

include_once('include/database.php');

function content() {
  $users = fetch_wol('*', 'users', 'TRUE', 'name ASC'); ?>
  <h2>Accounts</h2>

  <table>
    <?php foreach ($users as $u) { ?>
    <tr>
      <td class="name"><?php esc($u->name) ?></td>
    </tr>
    <?php } ?>
  </table>
<?php }

include('include/template.php');
