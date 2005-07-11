<?php
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
		require('/program files/apache group/apache/php/include/smarty/Smarty.class.php');
		require('classes/DB.php');
		require('classes/TaskManager.php');
		require('classes/Task.php');
		require('classes/Worktime.php');
		
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
		$tpl->template_dir = dirname(__FILE__);
		$tpl->compile_dir = dirname(__FILE__);
		$tpl->assign('taskManager', $this->taskManager);
		$tpl->display('phpworktimer.tpl');
	}
}
?>