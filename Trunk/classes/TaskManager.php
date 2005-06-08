<?php
class TaskManager {
	var $tasks;
	var $headTaskId;
	var $timerTurnedOn;
	
	function TaskManager($headTaskId) {
		$this->headTaskId = $headTaskId;
		$this->FillTasks();
	}
	
	function Stop() {
		pg_query("
			UPDATE worktime
			SET end_time = 'now'
			WHERE end_time IS NULL
		");
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
		$tasks = array();
		while ($assocTask = pg_fetch_assoc($rs)) {
			$task = new Task($assocTask);
			$tasks[$task->id] = $task;
		}
		
		
		// Filling worktimes for each task
		$rs = pg_query("
			SELECT
				worktime.id AS id,
				task,
				start_time,
				end_time,
				stop_time - start_time AS duration
			FROM worktime
				INNER JOIN task ON task.id = worktime.task
			WHERE parent ".($this->headTaskId ? " = $this->headTaskId" : "IS NULL")."
			ORDER BY stop_time
		");
		while ($assocWorktime = pg_fetch_assoc($rs)) {
			$worktime = new Worktime($assocWorktime);
	
			$this->tasks[$worktime->task]->AddWorktime($worktime);
	
			if ($this->tasks[$worktime->task]->isWorkingOn) {
				$this->timerTurnedOn = true;
			}
		}
	}
}
?>