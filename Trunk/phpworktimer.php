<?php

$phpworktimer = new phpworktimer();
$phpworktimer->main();

class phpworktimer {
	var $headTaskId;
	var $taskManager;
	var $taskId;
	var $taskName;
	
	function main() {
		$this->_Init();
		$this->_Input();
		$this->taskManager = new TaskManager($this->headTaskId);
		$this->_Process();
		$this->_Output();
	}
	
	function _Init() {
		define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT'].'/phpworktimer');
		define('DB_CONNECTION_STRING', 'host=localhost port=5432 user=uzver dbname=phpworktimer password=nlu');
		
		require('/program files/apache group/apache/php/include/smarty/Smarty.class.php');
		require('classes/TaskManager.php');
		require('classes/Task.php');
		require('classes/Worktime.php');
		
		$DBH = pg_connect(DB_CONNECTION_STRING);
		if (!is_resource($DBH) || pg_connection_status($DBH) != PGSQL_CONNECTION_OK)
		{
			exit(); // Error or notice has been shown
		}
		
		session_start();
	}
	
	function _Input() {
		$this->headTaskId = isset($_GET['headTaskId']) ? $_GET['headTaskId'] : NULL;
		if (!empty($this->headTaskId) && !preg_match('/^[0-9]+$/', $this->headTaskId)) {
			exit('Error: bad GET[headTaskId] = ' . $this->headTaskId);
		}
		
		if (isset($_GET['action'])) {
			$this->action = $_GET['action'];
			
			if (!in_array($this->action, array('add', 'edit', 'delete', 'start', 'stop'))) {
				exit('Error: bad GET[action] = ' . $_GET['action']);
			}
			
			if (in_array($this->action, array('add', 'edit'))) {
				if (empty($_GET['taskName'])) {
					exit('Error: empty GET[taskName]');
				}
				
				$this->taskName = $_GET['taskName'];
			}
			
			if (in_array($this->action, array('edit', 'delete', 'start'))) {
				if (empty($_GET['taskId'])) {
					exit('Error: empty GET[taskId]');
				}
				
				$this->taskId = $_GET['taskId'];
			}
		}
	}
	
	function _Process() {
		if (isset($this->action)) {
			switch ($this->action) {
				case 'add': $this->taskManager->AddTask($this->taskName); break;
				case 'edit': $this->taskManager->EditTask($this->taskId, $this->taskName); break;
				case 'delete': $this->taskManager->DeleteTask($this->taskId); break;
				case 'start': $this->taskManager->Start($this->taskId); break;
				case 'stop': $this->taskManager->Stop(); break;
			}
		}
	}
	
	function _Output() {
		$tpl = new Smarty;
		$tpl->template_dir = ROOT_DIR;
		$tpl->compile_dir = ROOT_DIR;
		$tpl->assign('taskManager', $this->taskManager);
		$tpl->display('phpworktimer.tpl');
	}
}
?>