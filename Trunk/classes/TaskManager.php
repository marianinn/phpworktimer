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
		$this->tasks[$taskId]->Start();
		$this->activeTaskId = $taskId;
	}
	
	function Stop() {
		if (!$this->activeTaskId) {
			return false;
		}
		$this->tasks[$this->activeTaskId]->Stop();
		$this->activeTaskId = NULL;
	}
	
	function DeleteTask($taskId) {
		$this->tasks[$taskId]->Delete();
		unset($this->tasks[$taskId]);
	}
	
	function FillTasks() {
		$rs = pg_query("
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
		while ($assocTask = pg_fetch_assoc($rs)) {
			$task = new Task($assocTask);
			$this->tasks[$task->id] = $task;
		}
		
		
		// Filling worktimes for each task
		$rs = pg_query("
			SELECT
				worktime.id AS id,
				task,
				start_time,
				stop_time,
				stop_time - start_time AS duration
			FROM worktime
				INNER JOIN task ON task.id = worktime.task
			WHERE parent ".($this->headTaskId ? " = $this->headTaskId" : "IS NULL")."
			ORDER BY stop_time DESC
		");
		while ($assocWorktime = pg_fetch_assoc($rs)) {
			$worktime = new Worktime($assocWorktime);
	
			$this->tasks[$worktime->taskId]->AddWorktime($worktime);
	
			if ($this->tasks[$worktime->taskId]->activeWorktimeId) {				
				$this->activeTaskId = $this->tasks[$worktime->taskId]->id;
			}
		}
	}
	
	function _SetPath() {
		$parentTaskId = $this->headTaskId;
		while ($parentTaskId != NULL && count($this->path) <= 3) {
			$rs = pg_query("
				SELECT id, name, parent
				FROM task
				WHERE id = $parentTaskId
			");
	
			$task = new Task(pg_fetch_assoc($rs));
			$this->path[] = $task;
			$parentTaskId = $task->parentTaskId;
		}
		$this->path = array_reverse($this->path);
	}
}
?>