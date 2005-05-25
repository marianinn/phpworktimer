<?php

define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT'].'/phpworktimer');
define('DB_CONNECTION_STRING', 'host=localhost port=5432 user=uzver dbname=phpworktimer password=nlu');

require('/program files/apache group/apache/php/include/smarty/Smarty.class.php');

global $DBH;// resource link to DB connection


$DBH = pg_connect(DB_CONNECTION_STRING);
if (!is_resource($DBH) || pg_connection_status($DBH) != PGSQL_CONNECTION_OK)
{
	exit; // Error or notice has just shown
}

$action = strrchr($_SERVER['REQUEST_URI'], '/');
$action = substr($action, 1, strpos($action . '.', '.') - 1);
if (!in_array($action, array('show', 'start', 'end', 'add', 'edit', 'delete'))) {
	$action = 'show';
}
require(ROOT_DIR.'/'.$action.'.php');


?>