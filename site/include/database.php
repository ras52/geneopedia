<?php

include_once('include/utils.php');

function db_connect() {
  global $dbh, $config;
  if ($dbh) return;

  $cfg = $config['database'];
  
  $dbh = mysql_connect( $cfg['hostname'], $cfg['username'],
                        $cfg['password'] )
    or die('Could not connect to database: '. mysql_error() );
 
  global $read_only_db;
  if (file_exists('include/readonly.php'))
    $read_only_db = 1;
    
  $db_name = $read_only_db ? $cfg['dasebase_ro'] : $cfg['database'];
  mysql_select_db($db_name, $dbh) or die('Could not select database');

  mysql_set_charset('utf8', $dbh)
    or die('Could not set database character set');
}

function maybe_log($sql) {
  global $config;
  $cfg = $config['database'];
  if (array_key_exists('log_sql', $cfg) && $cfg['log_sql']) 
    error_log($sql);
}

function exec_sql($sql) {
  global $dbh;
  maybe_log($sql);
  if (!$dbh) db_connect();
  $result = mysql_query($sql, $dbh);
  if (!$result) die('Cannot execute SQL: ' . mysql_error($dbh));
  return $result;
}

function fetch_one_cell($sql) {
  $row = mysql_fetch_array(exec_sql($sql), MYSQL_NUM);
  return $row[0];
}

function fetch_objs_with_sql($sql) {
  $result = exec_sql($sql);

  $objs = array();
  while ($obj = mysql_fetch_object($result))
    array_push($objs, $obj);

  return $objs;
}

function fetch_wghol($fields, $tables, $where, $groupby = null, $having = null,
                     $order = null, $limit = null, $offset = null) {
  if (!$fields) $sql = "SELECT *";
  else $sql = "SELECT $fields";

  if (!$tables) die('Missing table name');
  $sql .= " FROM $tables";

  if ($where) $sql .= " WHERE $where";
  if ($groupby) $sql .= " GROUP BY $groupby";
  if ($having) $sql .= " HAVING $having";
  if ($order) $sql .= " ORDER BY $order";

  if ($limit) {
    $sql .= sprintf(" LIMIT %d", $limit);
    if ($offset) $sql .= sprintf(" OFFSET %d", $offset);
  }

  return fetch_objs_with_sql($sql);
}

function fetch_wol($fields, $tables, $where, 
                   $order = null, $limit = null, $offset = null) {
  return fetch_wghol($fields, $tables, $where, null, null, 
                     $order, $limit, $offset);
}

function simple_where_clause($key, $id) {
  global $dbh;
  if (!$dbh) db_connect();
  return sprintf("%s='%s'", $key, mysql_real_escape_string($id, $dbh));
}

function fetch_all($table, $key, $id, $order = null) {
  return fetch_wghol('*', $table, simple_where_clause($key, $id), 
                     null, null, $order);
}

function fetch_one_or_none($table, $key, $id, $fields = null) {
  global $dbh;
  if (!$dbh) db_connect();
  $where = sprintf("%s='%s'", $key, mysql_real_escape_string($id, $dbh));
  $objs = fetch_wol($fields, $table, $where);
  if (count($objs)) return $objs[0];
  else return null;
}

function fetch_one($table, $key, $id, $fields = null) {
  $obj = fetch_one_or_none($table, $key, $id, $fields)
    or die('No such '.$table);
  return $obj;
}

function insert_array_contents($table, $fields) {
  global $dbh;
  if (!$dbh) db_connect();

  # Generate both fields simultaneously, as it's not clear array_keys
  # is guaranteed to return in a consistent ordering (although empirically
  # it does seem to).
  $values = array(); $keys = array();
  foreach ( array_keys($fields) as $field ) {
    array_push( $keys, $field );
    array_push( $values, sprintf("'%s'", 
      mysql_real_escape_string($fields[$field], $dbh) ) );
  }

  $sql = 'INSERT INTO ' . $table . ' (' . join(', ', $keys) . ')'
       . ' VALUES (' . join(', ', $values) . ')';
  exec_sql($sql);
  return mysql_insert_id($dbh);
}

function update_where($table, $fields, $where) {
  global $dbh;
  $sets = array();
  foreach ( array_keys($fields) as $field ) {
    if (isset($fields[$field]))
      array_push( $sets, sprintf( "%s='%s'", $field, 
        mysql_real_escape_string($fields[$field], $dbh) ) );
    else 
      array_push( $sets, sprintf( "%s=NULL", $field) );
  }

  $sql = "UPDATE $table SET " . join(', ', $sets) . " WHERE $where";
  exec_sql($sql);
}

function update_all($table, $fields, $key, $id) {
  update_where($table, $fields, simple_where_clause($key, $id));
}

function update_local_object(&$obj, $sets) {
  $o = (array)$obj;
  foreach (array_keys($sets) as $k)
    $o[$k] = $sets[$k];
  $obj = (object)$o;
}

