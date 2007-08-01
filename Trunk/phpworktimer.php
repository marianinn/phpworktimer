<?php
class phpworktimer {
	var $input;
	var $errorMsg;
	var $taskManager;
	var $statistics;

	function main() {
		$this->_Init();
		$this->_Input();
		$this->_Process();
		$this->statistics = new Statistics($this->input['headTaskId']);
		$this->_Output();
	}

	function _Init() {
		global $CFG;

		require($CFG['root_dir'] . '/v.php');
		require($CFG['smarty_dir'] . '/Smarty.class.php');
		require($CFG['classes_dir'] . '/DB.php');
		require($CFG['classes_dir'] . '/TaskManager.php');
		require($CFG['classes_dir'] . '/Task.php');
		require($CFG['classes_dir'] . '/Worktime.php');
		require($CFG['classes_dir'] . '/Statistics.php');

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
					'addTask',
					'editTask',
					'deleteTask',
					'startTask',
					'stop',
					'editWorktime',
					'deleteWorktime'
				)
			)) {
				exit('Error: bad GET[action] = ' . $_GET['action']);
			}

			if (in_array($this->input['action'],
				array('addTask', 'editTask'))
			) {
				if (empty($_GET['taskName'])) {
					exit('Error: empty GET[taskName]');
				}

				$this->input['taskName'] = $_GET['taskName'];
			}

			if (in_array($this->input['action'], array('editTask'))) {
				if (empty($_GET['taskRate'])) {
					exit('Error: empty GET[taskRate]');
				}

				$this->input['taskRate'] = $_GET['taskRate'];
			}

			if (in_array($this->input['action'],
				array('editTask', 'deleteTask', 'startTask'))
			) {
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
		$this->taskManager = new TaskManager();

		if ($this->errorMsg =
			$this->taskManager->Load($this->input['headTaskId'])
		)
		{
			return;
		}

		if (isset($this->input['action'])
			&& isset($_GET['key'])
			&& $_GET['key'] == $_SESSION['key']
		) {
			switch ($this->input['action']) {
				case 'addTask':
					$this->errorMsg = $this->taskManager->AddTask($this->input['taskName']);
					break;
				case 'editTask':
					$this->errorMsg = $this->taskManager->EditTask(
						$this->input['taskId'],
						$this->input['taskName'],
						$this->input['taskRate']
					);
					break;
				case 'deleteTask':
					$this->errorMsg = $this->taskManager->DeleteTask($this->input['taskId']);
					break;
				case 'startTask':
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
		global $CFG;

		$tpl = new Smarty;
		$tpl->template_dir = $CFG['smarty_template_dir'] . '/';
		$tpl->compile_dir = $CFG['smarty_compile_dir'] . '/';
		$tpl->assign('taskManager', $this->taskManager);
		$tpl->assign('statistics', $this->statistics);
		$tpl->assign('key', $_SESSION['key']);
		$tpl->assign('errorMsg', $this->errorMsg);
		$tpl->display($CFG['smarty_template_dir'] . '/phpworktimer.tpl');
	}
}
?>
