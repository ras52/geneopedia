#!/usr/bin/php
<?php
set_include_path('../site');
include_once('include/database.php');
unset($config['database']['log_sql']);

exec_sql("DELETE FROM users WHERE date_verified IS NULL AND "
         . "DATEDIFF( NOW(), date_registered ) >= 7");
