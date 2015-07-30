<?php 

include_once('include/utils.php');

header('Content-Type: text/html; charset=utf-8'); 

function do_page_content() {
  global $error_page; 
  if (isset($error_page)) $error_page();
  else content();
}

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php esc($config['title']) ?></title>
    <link rel="icon" href="<?php esc($config['http_path']) 
          ?>favicon.ico" sizes="16x16" type="image/vnd.microsoft.icon" />
    <link rel="stylesheet" href="<?php esc($config['http_path'])
          ?>style.css" type="text/css" />
    <?php if (function_exists('header_content')) header_content() ?>
  </head>
  <body>
    <table id="page-table">
      <tr id="page-header">
        <td id="left-header"><img src="<?php esc($config['http_path'])
          ?>siteicon.png" alt="<?php esc($config['title']) ?>" /></td>
        <td id="right-header"><h1><a href="."><?php 
          esc($config['title']) ?></a></h1></td>
      </tr>
      <tr id="page-main">
        <td id="page-menu">
          <?php include('include/menu.php'); ?>
        </td>
        <td id="page-content">
          <?php do_page_content(); ?>
        </td>
      </tr>
      <tr>
        <td id="page-footer" colspan="2">
          <p>Copyright © 2015, Richard Smith.</p>
        </td>
      </tr>
    </table>
  </body>
</html>
