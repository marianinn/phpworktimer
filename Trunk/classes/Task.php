<?php
class Task {
	var $id;
	var $name;
	var $parentTaskId;
	var $total;
	var $cost;
	var $activeWorktimeId;
	var $worktimes;
	
	function Task($assocTask) {
		$this->id = $assocTask['id'];
		$this->name = $assocTask['name'];
		$this->total = isset($assocTask['total']) ? $assocTask['total'] : NULL;
		$this->parentTaskId = isset($assocTask['parent']) ? $assocTask['parent'] : NULL;
		$this->worktimes = array();
		$this->activeWorktimeId = NULL;
		
		if ($this->total) {
			$this->_CountCost();
		}
	}
	
	function AddWorktime($worktime) {
		$this->worktimes[$worktime->id] = $worktime;
		if (!$worktime->stopTime) {
			if ($this->activeWorktimeId) {
				// we output error worktime anyway
				return 'Exception: already have active worktime';
			}
			$this->activeWorktimeId = $worktime->id;
		}
	}
	
	function Start() {
		if ($this->activeWorktimeId) {
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
			WHERE id = currval('worktime_id_seq');
		");
		$worktime = new Worktime($db->fetch_assoc($rs));
		
		list($this->activeWorktimeId) = $worktime->id;
		
		$old_worktimes = $this->worktimes;
		$this->worktimes = array($worktime->id => $worktime);
		foreach ($old_worktimes as $worktime) {
			$this->worktimes[$worktime->id] = $worktime;
		}
	}
	
	function Stop() {
		$this->worktimes[$this->activeWorktimeId]->Stop();
		$this->activeWorktimeId = NULL;
	}
	
	/**
	 * Counts $this->cost and parses $this->total
	 */
	function _CountCost() {
		if (preg_match('/^([0-9]+) day(s)?/', $this->total, $matches)) {
			$days = $matches[1];
			$this->total = substr(strrchr($this->total, ' '), 1);
		}
		list($hours, $minutes, $seconds) = explode(':', $this->total);
		$hours += empty($days) ? 0 : $days * 24;
		
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
		$this->cost = round(4*($hours + $minutes/60), 2);
	}
	
	function Delete() {
		$db = &$this->_getDb();
		$db->query("
			DELETE FROM task
			WHERE id = $this->id
		");
	}
	
	function &_getDb() {
		return new DB;
	}
}
?>