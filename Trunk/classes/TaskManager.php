<?php
class TaskManager {
	var $headTaskId;
	var $activeTaskId;
	var $tasks = array();
	var $path = array();
	
	function TaskManager($headTaskId) {
		$this->headTaskId = $headTaskId;
		$this->FillTasks();
		$this->_SetPath();
	}
	
	function Start($taskId) {
		if ($this->activeTaskId) {
			return false;
		}
		$this->activeTaskId = $taskId;
		return $this->tasks[$taskId]->Start();
	}
	
	function Stop() {
		if (!$this->activeTaskId) {
			return 'Exception: already active';
		}
		if ($result = $this->tasks[$this->activeTaskId]->Stop()) {
			return $result;
		}
		$this->activeTaskId = NULL;
	}
	
	function DeleteTask($taskId) {
		$this->tasks[$taskId]->Delete();
		unset($this->tasks[$taskId]);
	}
	
	function FillTasks() {
		$db = &$this->_getDb();
		
		$rs = $db->query("
			SELECT
				task.id,
				name,
				SUM(stop_time - start_time) AS total
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
				stop_time - start_time AS duration
			FROM worktime
				INNER JOIN task ON task.id = worktime.task
			WHERE parent ".($this->headTaskId ? " = $this->headTaskId" : "IS NULL")."
			ORDER BY id DESC
		");
		while ($assocWorktime = $db->fetch_assoc($rs)) {
			$worktime = new Worktime($assocWorktime);
	
			$this->tasks[$worktime->taskId]->AddWorktime($worktime);
	
			if ($this->tasks[$worktime->taskId]->activeWorktimeId) {				
				$this->activeTaskId = $this->tasks[$worktime->taskId]->id;
			}
		}
	}
	
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