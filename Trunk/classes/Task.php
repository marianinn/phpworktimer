<?php
class Task {
	var $id;
	var $name;
	var $parentTaskId;
	var $total;
	var $activeWorktimeId;
	var $worktimes;
	
	function Task($assocTask) {
		$this->id = $assocTask['id'];
		$this->name = $assocTask['name'];
		$this->total = isset($assocTask['total']) ? $assocTask['total'] : NULL;
		$this->parentTaskId = isset($assocTask['parent']) ? $assocTask['parent'] : NULL;
		$this->isWorkingOn = false;
		
		if ($this->total) {
			$this->_CountCost();
		}
	}
	
	function AddWorktime($worktime) {
		$this->worktimes[] = $worktime;
		if (!$worktime->stopTime) {		
			$this->activeWorktimeId = $worktime->id;
		}
	}
	
	function Start() {
		pg_query("
			INSERT INTO worktime(task, start_time)
			VALUES($this->id, 'now')
		");
		$rs = pg_query("
			SELECT *
			FROM worktime
			WHERE id = currval('worktime_id_seq');
		");
		$worktime = new Worktime(pg_fetch_assoc($rs));
		list($this->activeWorktimeId) = $worktime->id;
		$this->worktimes[] = $worktime;
	}
	
	function Stop() {
		$this->activeWorktime->Stop();
		$this->activeWorktime = NULL;
	}
	
	/**
	 * Counts $this->cost and parses $this->total
	 */
	function _CountCost() {
		$total = explode(':', $this->total);
		if ($total[2] >= 30) {
			$total[1]++;
			if ($total[1] < 10) {
				$total[1] = '0' . $total[1];
			}
			elseif ($total[1] == 60) {
				$total[1] = '00';
				$total[0]++;
			}
		}
		$this->total = (int)$total[0] . ':' . $total[1];
		$this->cost = round(4*($total[0] + $total[1]/60), 2);
	}
	
	function Delete() {
		pg_query("
			DELETE FROM task
			WHERE id = $this->id
		");
	}
}
?>