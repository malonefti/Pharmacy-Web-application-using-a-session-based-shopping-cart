<?php
require_once("config.php");
$dbcon = mysql_connect(dbHost,dbUser,dbPass);
if (!$dbcon) {
    die('No db connection: ' . mysql_error());
}
$database = mysql_select_db(dbName,$dbcon);
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET 'utf8'");

?>