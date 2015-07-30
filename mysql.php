#!/usr/bin/php
<?php
$config = parse_ini_file('site/include/config.ini', true);
$cfg = $config['database'];

pcntl_exec('/usr/bin/mysql', array( $cfg['database'],
  '-u'.$cfg['username'], '-p'.$cfg['password'], '-h'.$cfg['hostname'] ));


