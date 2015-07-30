<?php 

header('Content-Type: text/html; charset=utf-8'); 

global $config;
$config = parse_ini_file('include/config.ini');

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $config['title'] ?></title>
    <link rel="icon" href="favicon.ico" sizes="16x16" 
          type="image/vnd.microsoft.icon" />
    <link rel="stylesheet" href="style.css" type="text/css" />
  </head>
  <body>
    <table id="page-table" border="0" cellspacing="0" cellpadding="0">
      <tr id="page-header">
        <td id="left-header"><img src="geneopedia.png" 
          alt="<?php echo $config['title'] ?>" /> </td>
        <td id="right-header"><h1><a href="<?php print $root
          ?>"><?php echo $config['title'] ?></a></h1></td>
      </tr>
      <tr id="page-main">
        <td id="page-menu">
          <?php include('include/menu.php'); ?>
        </td>
        <td id="page-content">
          <?php content(); ?>
        </td>
      </tr>
      <tr>
        <td id="page-footer" colspan="2">Copyright © 2015, Richard Smith.</td>
      </tr>
    </table>
  </body>
</html>
