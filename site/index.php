<?php 

function content() { 
  global $config; ?>
  <p>Welcome to <?php esc($config['title']) ?>.</p>
<?php }

include('include/template.php');
