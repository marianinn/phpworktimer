<?php
class phpWorktimer {
	var $input;
	var $errorMsg;

	function main() {
		$this->_Init();
		$this->_Input();
		$this->taskManager = new TaskManager($this->input['headTaskId']);
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
		if (!isset($_SESSION['key'])) {
			$_SESSION['key'] = 1;

			// to prevent bug
			if (isset($_GET['key']) && $_GET['key'] == 1) {
				$_SESSION['key'] = 2;
			}
		}
	}

	function _Input() {
		$this->input['headTaskId'] = isset($_GET['headTaskId']) ? $_GET['headTaskId'] : NULL;

		if (!empty($this->input['headTaskId'])
			&& !preg_match('/^[0-9]+$/', $this->input['headTaskId'])
		) {
			exit('Error: bad GET[headTaskId] = ' . $this->input['headTaskId']);
		}

		if (isset($_GET['action'])) {
			$this->input['action'] = $_GET['action'];

			if (!in_array($this->input['action'],
				array(
					'add',
					'rename',
					'delete',
					'start',
					'stop',
					'editWorktime',
					'deleteWorktime'
				)
			)) {
				exit('Error: bad GET[action] = ' . $_GET['action']);
			}

			if (in_array($this->input['action'], array('add', 'rename'))) {
				if (empty($_GET['taskName'])) {
					exit('Error: empty GET[taskName]');
				}

				$this->input['taskName'] = $_GET['taskName'];
			}

			if (in_array($this->input['action'], array('rename', 'delete', 'start'))) {
				if (empty($_GET['taskId'])) {
					exit('Error: empty GET[taskId]');
				}

				$this->input['taskId'] = $_GET['taskId'];
			}

			if (in_array($this->input['action'], array('editWorktime'))) {
				if (empty($_GET['worktimeId'])) {
					exit('Error: empty GET[worktimeId]');
				}
				if (empty($_GET['worktimeStartTime'])) {
					exit('Error: empty GET[worktimeStartTime]');
				}
				if (empty($_GET['worktimeStopTime'])) {
					exit('Error: empty GET[worktimeStopTime]');
				}

				$this->input['worktimeId'] = $_GET['worktimeId'];
				$this->input['worktimeStartTime'] = $_GET['worktimeStartTime'];
				$this->input['worktimeStopTime'] = $_GET['worktimeStopTime'];
			}

			if (in_array($this->input['action'], array('deleteWorktime'))) {
				if (empty($_GET['worktimeId'])) {
					exit('Error: empty GET[worktimeId]');
				}

				$this->input['worktimeId'] = $_GET['worktimeId'];
			}
		}
	}

	function _Process() {
		if (isset($this->input['action'])
			&& isset($_GET['key'])
			&& $_GET['key'] == $_SESSION['key']
		) {
			switch ($this->input['action']) {
				case 'add':
					$this->errorMsg = $this->taskManager->AddTask($this->input['taskName']);
					break;
				case 'rename':
					$this->errorMsg = $this->taskManager->RenameTask(
						$this->input['taskId'], $this->input['taskName']
					);
					break;
				case 'delete':
					$this->errorMsg = $this->taskManager->DeleteTask($this->input['taskId']);
					break;
				case 'start':
					$this->errorMsg = $this->taskManager->StartTask($this->input['taskId']);
					break;
				case 'stop':
					$this->errorMsg = $this->taskManager->Stop();
					break;
				case 'editWorktime':
					$this->errorMsg = $this->taskManager->EditWorktime(
						$this->input['worktimeId'], $this->input['worktimeStartTime'],
						$this->input['worktimeStopTime']
					);
					break;
				case 'deleteWorktime':
					$this->errorMsg = $this->taskManager->DeleteWorktime($this->input['worktimeId']);
					break;
			}
			$_SESSION['key']++;
		}
	}

	function _Output() {
		$tpl = new Smarty;
		$tpl->template_dir = dirname(__FILE__);
		$tpl->compile_dir = dirname(__FILE__);
		$tpl->assign('taskManager', $this->taskManager);
		$tpl->assign('key', $_SESSION['key']);
		$tpl->assign('errorMsg', $this->errorMsg);
		$tpl->display('phpworktimer.tpl');
	}
}
?>