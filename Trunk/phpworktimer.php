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
session_start();



if (empty($_GET['headTaskId'])) {
	exit('Error: empty GET[headTaskId]');
}
$taskManager = new TaskManager($_GET['headTaskId']);

if (isset($_GET['action'])) {
	$action = $_GET['action'];
	
	if (!in_array($action, array('add', 'edit', 'delete', 'start', 'stop'))) {
		exit('Error: bad GET[action] = ' . $_GET['action']);
	}
	if (in_array($action, array('add', 'edit'))) {
		if (empty($_GET['taskName'])) {
			exit('Error: empty GET[taskName]');
		}
		
		$taskName = $_GET['taskName'];
	}
	if (in_array($action, array('edit', 'delete', 'start'))) {
		if (empty($_GET['taskId'])) {
			exit('Error: empty GET[taskId]');
		}
		
		$taskId = $_GET['taskId'];
	}
	
	switch ($action) {
		case 'add': $taskManager->AddTask($taskName); break;
		case 'edit': $taskManager->EditTask($taskId, $TaskName); break;
		case 'delete': $taskManager->DeleteTask($taskId); break;
		case 'start': $taskManager->Start($taskId); break;
		case 'stop': $taskManager->Stop(); break;
	}
}

$tpl = new Smarty;
$tpl->template_dir = ROOT_DIR;
$tpl->compile_dir = ROOT_DIR;
$tpl->assign('timer_turned_on', $TaskManager->timerTurnedOn);
$tpl->assign('tasks', $TaskManager->tasks);
$tpl->assign('path', $TaskManager->Path());
$tpl->display('phpworktimer.tpl');


?>