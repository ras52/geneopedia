<?php 
include_once('include/utils.php');

function error_404_content() { ?>
  <h2>Error: 404 Not Found</h2>

  <p>The requested URL <tt><?php esc($_SERVER['REQUEST_URI']) ?></tt> 
    was not found on this server.</p>
<?php }
