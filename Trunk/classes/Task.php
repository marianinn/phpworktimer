<?php
class Task {
	var $id;
	var $name;
	var $parentTaskId;
	var $total;
	var $isWorkingOn;
	
	function Task($assocTask) {
		$this->id = $assocTask['id'];
		$this->name = $assocTask['name'];
		$this->total = $assocTask['total'];
		$this->parentTaskId = isset($assocTask['parent']) ? $assocTask['parent'] : NULL;
		$this->isWorkingOn = false;
		
		if ($this->total) {
			$this->_CountCost();
		}
	}
	
	function AddWorktime($assocWorktime) {
		$worktime = new Worktime($assocWorktime);
		$this->worktimes[] = $worktime;		
		$this->is_working_on = !$worktime->stop_time;
	}
	
	function Start() {
		pg_query("
			INSERT INTO worktime(task, start_time)
			VALUES($task_id, 'now')
		");
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