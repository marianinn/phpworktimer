<?php
class TaskManager {
	var $headTaskId;
	var $activeTaskId;
	var $tasks = array();
	var $path = array();
	var $worktimes = array();

	function TaskManager($headTaskId) {
		$this->headTaskId = $headTaskId;
		$this->_FillTasks();
		$this->_SetPath();
	}

	/**
	 * Adds task to DB and $this->tasks.
	 * @param string $taskName -- new task name
	 */
	function AddTask($taskName) {
		if (empty($taskName)) {
			return 'Exception: empty taskName';
		}

		$db = &$this->_getDb();

		$taskName = $db->escape_string($taskName);
		$db->query("
			INSERT INTO task(parent, name)
			VALUES(
				". ($this->headTaskId ? $this->headTaskId : 'NULL') ."
				,'$taskName'
			)
		");

		$this->_FillTasks();
	}

	function RenameTask($taskId, $taskName) {
		if ($result = $this->tasks[$taskId]->Rename($taskName)) {
			return $result;
		}
		$this->_FillTasks();
	}

	function StartTask($taskId) {
		if ($this->activeTaskId) {
			return 'Exception: there is active task already';
		}
		if (empty($this->tasks[$taskId])) {
			return 'Exception: invalid taskId';
		}
		
		$this->activeTaskId = $taskId;
		return $this->tasks[$taskId]->Start();
	}

	function Stop() {
		if (!$this->activeTaskId) {
			return 'Exception: there isn\'t any active task';
		}
		if ($result = $this->tasks[$this->activeTaskId]->Stop()) {
			return $result;
		}
		$this->_FillTasks();
	}

	function DeleteTask($taskId) {
		if (empty($this->tasks[$taskId])) {
			return 'Exception: invalid taskId';
		}
		
		$this->tasks[$taskId]->Delete();
		
		$this->_FillTasks();
	}

	function EditWorktime($worktimeId, $worktimeStartTime, $worktimeStopTime) {

		if (!isset($this->worktimeId2taskId[$worktimeId])) {
			return 'Exception: invalid worktimeId';
		}
		
		$result = $this->tasks[$this->worktimeId2taskId[$worktimeId]]->worktimes[$worktimeId]->Edit($worktimeStartTime, $worktimeStopTime);
		
		$this->_FillTasks();
		
		return $result;
	}

	function DeleteWorktime($worktimeId) {
		if (empty($this->worktimeId2taskId[$worktimeId])) {
			return 'Exception: invalid worktimeId';
		}
		
		$result = $this->tasks[$this->worktimeId2taskId[$worktimeId]]->DeleteWorktime($worktimeId);
		
		$this->_FillTasks();
		
		return $result;
	}

	function _FillTasks() {
		$db = &$this->_getDb();

		$this->tasks = array();
		$this->activeTaskId = NULL;

		// Fetch all tasks
		$rs = $db->query("
			SELECT
				task.id,
				name,
				to_hms(SUM(stop_time - start_time)) AS total
			FROM task
				LEFT JOIN worktime ON task.id = worktime.task
			WHERE parent ".($this->headTaskId ? " = $this->headTaskId" : "IS NULL")."
			GROUP BY task.id, name
			ORDER BY id DESC
		");

		while ($assocTask = $db->fetch_assoc($rs)) {
			$task = new Task($assocTask);
			$this->tasks[$task->id] = $task;
		}


		// Filling worktimes for each task
		$rs = $db->query("
			SELECT
				worktime.id AS id,
				task,
				start_time,
				stop_time,
				to_hms(stop_time - start_time) AS duration
			FROM worktime
				INNER JOIN task ON task.id = worktime.task
			WHERE parent ".($this->headTaskId ? " = $this->headTaskId" : "IS NULL")."
			ORDER BY id DESC
		");
		while ($assocWorktime = $db->fetch_assoc($rs)) {
			$worktime = new Worktime($assocWorktime);

			$this->tasks[$worktime->taskId]->AddWorktime($worktime);
			$this->worktimeId2taskId[$worktime->id] = $worktime->taskId; 
		}

		// Get active task
		$rs = $db->query("
			SELECT task
			FROM worktime
			WHERE stop_time IS NULL
		");

		if ($db->num_rows($rs)) {
			list($this->activeTaskId) = $db->fetch_row($rs);
		}
	}

	/**
	 * Sets $this->path array of tasks which are parents for current task
	 */
	function _SetPath() {
		$db = $this->_getDb();
		$parentTaskId = $this->headTaskId;
		while ($parentTaskId != NULL && count($this->path) <= 3) {
			$rs = $db->query("
				SELECT id, name, parent
				FROM task
				WHERE id = $parentTaskId
			");

			$task = new Task($db->fetch_assoc($rs));
			$this->path[] = $task;
			$parentTaskId = $task->parentTaskId;
		}
		$this->path = array_reverse($this->path);
	}

	function &_getDb() {
		return new DB;
	}
}
?>