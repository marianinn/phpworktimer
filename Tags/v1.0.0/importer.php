<?php
require('classes/DB.php');
$importer = new Importer();
$importer->main();

class Importer {
	var $srcDbName;
	var $dstDbName;

	function main() {

		if (empty($_GET['srcDbName'])) {
			die('Error: empty GET[srcDbName]');
		}
		if (empty($_GET['dstDbName'])) {
			die('Error: empty GET[dstDbName]');
		}

		$this->srcDbName = $_GET['srcDbName'];
		$this->dstDbName = $_GET['dstDbName'];

		list($tasks, $worktimes) = $this->FetchData();

		$tasksl = array();
		$patt = 0xffffffff;
		for ($i = 0; $i < 4; $i++) {
			foreach ($tasks as $key => $task) {
				if ($task['id'] & $patt
					&& !($task['id'] & $patt/256)
				) {
					$task['parent'] = $task['id'] & (0xffffffff ^ $patt);
					$tasksl[$i][$task['id']] = $task;
				}
			}
			$patt /= 256;
		}

		list($tasks, $worktimes) = $this->SaveData($tasksl, $worktimes);

		v($tasks);
		v($worktimes);
	}

	function SaveData($tasksl, $worktimes) {
		$db = &$this->_getDb('
			host=localhost
			port=5432
			user=uzver
			dbname='. $this->dstDbName .'
			password=nlu
		');

		$tasks = array();

		for ($i = 0; $i < 4; $i++) {
			foreach ($tasksl[$i] as $key => $task) {
				if($task['parent'] == 0) {
					$task['parent'] = 'NULL';
				}
				else {
					$task['parent'] = $tasks[$task['parent']]['new_id'];
				}

				$rs = $db->query('
					INSERT INTO task(name, parent)
					VALUES(
						\''. pg_escape_string($task['title']) .'\',
						'. $task['parent'] .'
					)
				');
				$rs = $db->query('
					SELECT CURRVAL(\'task_id_seq\')
				');

				list($tasks[$key]['new_id']) = $db->fetch_row($rs);
			}
		}

		foreach($worktimes as $worktime) {
			$rs = $db->query('
				INSERT INTO worktime(task, start_time, stop_time)
				VALUES(
					\''. $tasks[$worktime['task_id']]['new_id'] .'\',
					\''. $worktime['start_time'] .'\',
					\''. $worktime['end_time'] .'\'
				)
			');
		}

		return array($tasks, $worktimes);
	}

	function FetchData() {
		$db = &$this->_getDb('
			host=localhost
			port=5432
			user=uzver
			dbname='. $this->srcDbName .'
			password=nlu
		');

		$rs = $db->query('
			SELECT
				id,
				title
			FROM task
			ORDER BY id
		');
		$tasks = array();
		while ($task = $db->fetch_assoc($rs)) {
			$tasks[] = $task;
		}

		$rs = $db->query('
			SELECT id, task_id, start_time, end_time
			FROM worktime
			ORDER BY task_id, id
		');
		$worktimes = array();
		while ($worktime = $db->fetch_assoc($rs)) {
			$worktimes[] = $worktime;
		}

		return array($tasks, $worktimes);
	}

	function &_getDb($conn_str = NULL) {
		return new DB($conn_str);
	}
}

?>