<?php
class Task {
	var $id;
	var $name;
	var $rate;
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
		$this->rate = isset($assocTask['rate']) ? $assocTask['rate'] : NULL;
		$this->total = isset($assocTask['total']) ? $assocTask['total'] : NULL;
		$this->parentTaskId = isset($assocTask['parent']) ? $assocTask['parent'] : NULL;
		$this->worktimes = array();
		$this->activeWorktimeId = NULL;
		$this->isActive = FALSE;
		$this->ascendantTasksIds = $ascendantTasksIds;

		$this->_countCost();
	}

	function addWorktime($worktime) {

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

	function edit($name, $rate) {
		$name = trim($name);
		if (!$name) {
			return 'Exception: empty taskName';
		}
		if (!preg_match('/^([0-9]+\\.?[0-9]*)|([0-9]*\\.?[0-9]+)$/', $rate)) {
			return 'Exception: bad taskRate = "' . $rate . '"';
		}

		$db = &$this->_getDb();
		$db->query("
			UPDATE task
			SET
				name = '". $db->escape_string($name) ."',
				rate = " . $db->escape_string($rate) ."
			WHERE id = $this->id
		");
	}

	function start() {
		if ($this->isActive) {
			return 'Exception: already started';
		}

		$db = &$this->_getDb();
		$rs = $db->query("BEGIN");
		$now = date('Y-m-d H:i:s O');

		$rs = $db->query("
			INSERT INTO worktime(task, start_time)
			VALUES(". $this->id .", '". $now ."')
		");

		$worktime_id = $db->last_insert_id('worktime');

		$rs = $db->query("
			SELECT *, stop_time - start_time AS duration
			FROM worktime
			WHERE id = $worktime_id
		");
		$worktime = new Worktime($db->fetch($rs));

		list($this->activeWorktimeId) = $worktime->id;
		$this->isActive = TRUE;

		$old_worktimes = $this->worktimes;
		$this->worktimes = array($worktime->id => $worktime);
		foreach ($old_worktimes as $worktime) {
			$this->worktimes[$worktime->id] = $worktime;
		}


		// Update all ascendant tasks.order_time
		$tasksIds = $this->ascendantTasksIds;
		$tasksIds[] = $this->id;
		$rs = $db->query("
			UPDATE task
			SET order_time = '". $now ."'
			WHERE id IN(".join(', ', $tasksIds).")
		");

		$rs = $db->query("COMMIT");
	}

	function stop() {
		if (!$this->isActive) {
			return 'Exception: already stopped';
		}

		$result = $this->worktimes[$this->activeWorktimeId]->stop();
		$this->activeWorktimeId = NULL;
		$this->isActive = FALSE;

		return $result;
	}

	function delete() {
		$db = &$this->_getDb();
		$db->query("
			DELETE FROM task
			WHERE id = $this->id
		");
	}

	function deleteWorktime($worktimeId) {
		if (isset($this->worktimes[$worktimeId])) {
			$this->worktimes[$worktimeId]->delete();
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
	function _countCost() {
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
			$this->cost = round($this->rate*($hours + $minutes/60), 2);
		}
		else {
			$this->cost = NULL;
		}
	}

	function &_getDb() {
		return new DB();
	}
}
?>