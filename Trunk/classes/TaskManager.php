<?php
class TaskManager {
	var $headTask;
	var $activeTaskId;
	var $tasks = array();
	var $path = array();
	var $ascendantTasksIds = array();
	var $worktimes = array();

	function TaskManager() {
	}

	function load($headTaskId) {
		if ($headTaskId) {
			if ($result = $this->_fillHeadTask($headTaskId)) {
				return $result;
			}
		}
		if ($this->headTask) {
			$this->ascendantTasksIds = $this->_getAscendantTasksIds($this->headTask->id);
		}
		$this->_fillTasks();
		$this->_fillPath();
	}

	/**
	 * Adds task to DB and $this->tasks.
	 * @param string $taskName -- new task name
	 */
	function addTask($taskName) {
		if (empty($taskName)) {
			return 'Exception: empty taskName';
		}

		$db = &$this->_getDb();

		$db->query("
			INSERT INTO task(
				parent,
				name,
				rate
			)
			VALUES(
				". ($this->headTask ? $this->headTask->id : 'NULL') ."
				,'" . $db->escape_string($taskName) . "'
				,". ($this->headTask ? $this->headTask->rate : 1) ."
			)
		");

		$this->_fillTasks();
	}

	function editTask($taskId, $taskName, $taskRate) {
		if ($result = $this->tasks[$taskId]->edit($taskName, $taskRate)) {
			return $result;
		}
		$this->_fillTasks();
	}

	function startTask($taskId) {
		if ($this->activeTaskId) {
			return 'Exception: there is active task already';
		}
		if (empty($this->tasks[$taskId])) {
			return 'Exception: invalid taskId';
		}

		$this->activeTaskId = $taskId;
		return $this->tasks[$taskId]->start();
	}

	function stop() {
		if (!$this->activeTaskId || empty($this->tasks[$this->activeTaskId])) {
			return 'Exception: there isn\'t any active task';
		}
		if ($result = $this->tasks[$this->activeTaskId]->stop()) {
			return $result;
		}
		$this->_fillTasks();
	}

	function deleteTask($taskId) {
		if (empty($this->tasks[$taskId])) {
			return 'Exception: invalid taskId';
		}

		$this->tasks[$taskId]->delete();

		$this->_fillTasks();
	}

	function editWorktime($worktimeId, $worktimeStartTime, $worktimeStopTime) {
		if (!isset($this->worktimeId2taskId[$worktimeId])) {
			return 'Exception: invalid worktimeId';
		}

		$result = $this->tasks[$this->worktimeId2taskId[$worktimeId]]
			->worktimes[$worktimeId]->edit(
				$worktimeStartTime,
				$worktimeStopTime
		);

		$this->_fillTasks();

		return $result;
	}

	function deleteWorktime($worktimeId) {
		if (empty($this->worktimeId2taskId[$worktimeId])) {
			return 'Exception: invalid worktimeId';
		}

		$result = $this->tasks[$this->worktimeId2taskId[$worktimeId]]
			->deleteWorktime($worktimeId);

		$this->_fillTasks();

		return $result;
	}

	function _fillTasks() {
		$db = &$this->_getDb();

		$this->tasks = array();
		$this->activeTaskId = NULL;

		// Fetch all tasks
		$rs = $db->query("
			SELECT
				task.id,
				name,
				rate,
				to_hms(SUM(stop_time - start_time)) AS total,
				order_time
			FROM task
				LEFT JOIN worktime ON task.id = worktime.task
			WHERE parent ".($this->headTask ? " = ". $this->headTask->id : "IS NULL")."
			GROUP BY task.id, name, rate, order_time
			ORDER BY order_time DESC, id DESC
		");

		$tmp = $this->ascendantTasksIds;
		if (!empty($this->headTask->id)) {
			$tmp = array_merge($this->ascendantTasksIds, array($this->headTask->id));
		}
		while ($assocTask = $db->fetch($rs)) {
			$task = new Task($assocTask, $tmp);
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
			WHERE parent ".($this->headTask->id ? " = ". $this->headTask->id : "IS NULL")."
			ORDER BY id DESC
		");
		while ($assocWorktime = $db->fetch($rs)) {
			$worktime = new Worktime($assocWorktime);

			$this->tasks[$worktime->taskId]->addWorktime($worktime);
			$this->worktimeId2taskId[$worktime->id] = $worktime->taskId;
		}


		// Determine active task
		$this->_activeTaskId();
		if ($this->activeTaskId) {
			foreach ($this->_getAscendantTasksIds($this->activeTaskId)
				as $taskId
			) {
				if (in_array($taskId, array_keys($this->tasks))) {
					$this->tasks[$taskId]->isActive = TRUE;
				}
			}
		}
	}

	/**
	 * Determines id of active task.
	 */
	function _activeTaskId() {
		$db = &$this->_getDb();
		$rs = $db->query("
			SELECT task
			FROM worktime
			WHERE stop_time IS NULL
		");

		if ($db->num_rows($rs)) {
			list($this->activeTaskId) = $db->fetch($rs);
		}
	}

	/**
	 * Sets $this->path array of at most 3 tasks which are parents for current task
	 */
	function _fillPath() {
		$db = &$this->_getDb();
		$parentTaskId = $this->headTask->id;
		while ($parentTaskId != NULL && count($this->path) <= 3) {
			$rs = $db->query("
				SELECT id, name, parent
				FROM task
				WHERE id = $parentTaskId
			");

			$task = new Task($db->fetch($rs));
			$this->path[] = $task;
			$parentTaskId = $task->parentTaskId;
		}
		$this->path = array_reverse($this->path);
	}

	/**
	 * Finds all ascendant tasks ids.
	 * @param taskId for which to find ascendant tasks
	 * @return array of ids
	 */
	function _getAscendantTasksIds($taskId) {
		$db = &$this->_getDb();
		$ascendantTasksIds = array($taskId);
		for ($i = 0; $ascendantTasksIds[$i]; $i++) {
			$rs = $db->query("
				SELECT parent
				FROM task
				WHERE id = ".$ascendantTasksIds[$i]."
			");

			list($ascendantTasksIds[]) = $db->fetch($rs);
		}

		unset($ascendantTasksIds[$i]);
		unset($ascendantTasksIds[0]);
		$ascendantTasksIds = array_reverse($ascendantTasksIds);

		return $ascendantTasksIds;
	}

	function _fillHeadTask($headTaskId) {
		$db = &$this->_getDb();

		if (!preg_match('/^[0-9]+$/', $headTaskId))
		{
			return 'Exception: bad headTaskId';
		}

		$rs = $db->query("
			SELECT
				task.id,
				name,
				rate
			FROM task
			WHERE id = ". $headTaskId ."
		");

		if (!$db->num_rows($rs)) {
			return 'Exception: bad headTaskId';
		}

		$this->headTask = &new Task($db->fetch($rs));
	}

	function &_getDb() {
		$db = &new DB();
		return $db;
	}
}
?>