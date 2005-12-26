<?php
class Task {
	var $id;
	var $name;
	var $parentTaskId;
	var $total;
	var $cost;
	var $activeWorktimeId;
	var $isActive;
	var $worktimes;
	var $ascendantTasksIds;

	function Task($assocTask, $ascendantTasksIds = array()) {
		$this->id = $assocTask['id'];
		$this->name = $assocTask['name'];
		$this->total = isset($assocTask['total']) ? $assocTask['total'] : NULL;
		$this->parentTaskId = isset($assocTask['parent']) ? $assocTask['parent'] : NULL;
		$this->worktimes = array();
		$this->activeWorktimeId = NULL;
		$this->isActive = FALSE;
		$this->ascendantTasksIds = $ascendantTasksIds;
		
		$this->_CountCost();
	}

	function AddWorktime($worktime) {

		$this->worktimes[$worktime->id] = $worktime;

		if (!$worktime->stopTime) {
			if ($this->activeWorktimeId) {
				// we output error worktime anyway
				return 'Exception: already have active worktime';
			}
			$this->activeWorktimeId = $worktime->id;
			$this->isActive = TRUE;
		}
	}

	function Rename($name) {
		$name = trim($name);
		if (!$name) {
			return 'Exception: empty taskName';
		}

		$db = &$this->_getDb();
		$db->query("
			UPDATE task
			SET name = '". pg_escape_string($name) ."'
			WHERE id = $this->id
		");
	}

	function Start() {
		if ($this->isActive) {
			return 'Exception: already started';
		}

		$db = &$this->_getDb();
		$db->query("
			INSERT INTO worktime(task, start_time)
			VALUES($this->id, 'now')
		");
		$rs = $db->query("
			SELECT *, stop_time - start_time AS duration
			FROM worktime
			WHERE id = currval('worktime_id_seq')
		");
		$worktime = new Worktime($db->fetch_assoc($rs));

		list($this->activeWorktimeId) = $worktime->id;
		$this->isActive = TRUE;

		$old_worktimes = $this->worktimes;
		$this->worktimes = array($worktime->id => $worktime);
		foreach ($old_worktimes as $worktime) {
			$this->worktimes[$worktime->id] = $worktime;
		}
		
		// Update all ascendant tasks.order_time
		
		$tasksIds = $this->ascendantTasksIds + array($this->id);
		$rs = $db->query("
			UPDATE task
			SET order_time = 'now'
			WHERE id IN(".join(', ', $tasksIds).")
		");
	}

	function Stop() {
		if (!$this->isActive) {
			return 'Exception: already stopped';
		}

		$result = $this->worktimes[$this->activeWorktimeId]->Stop();
		$this->activeWorktimeId = NULL;
		$this->isActive = FALSE;

		return $result;
	}

	function Delete() {
		$db = &$this->_getDb();
		$db->query("
			DELETE FROM task
			WHERE id = $this->id
		");
	}
	
	function DeleteWorktime($worktimeId) {
		if (isset($this->worktimes[$worktimeId])) {
			$this->worktimes[$worktimeId]->Delete();
			unset($this->worktimes[$worktimeId]);
			if ($worktimeId == $this->activeWorktimeId) {
				$this->activeWorktimeId = NULL;
				$this->isActive = FALSE;
			}
		}
		else {
			return 'Exception: invalid worktimeId';
		}
	}

	/**
	 * Counts $this->cost and parses $this->total
	 */
	function _CountCost() {
		if ($this->total) {
			list($hours, $minutes, $seconds) = explode(':', $this->total);

			if ($seconds >= 30) {
				$minutes++;
				if ($minutes < 10) {
					$minutes = '0' . $minutes;
				}
				elseif ($minutes == 60) {
					$minutes = '00';
					$hours++;
				}
			}
			$this->total = (int)$hours . ':' . $minutes;
			$this->cost = round(5*($hours + $minutes/60), 2);
		}
		else {
			$this->cost = NULL;
		}
	}

	function &_getDb() {
		return new DB;
	}
}
?>