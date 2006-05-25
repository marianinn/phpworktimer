<?php
class Statistics {
	var $today = array();
	var $group = array();

	function Statistics($headTaskId) {

		$this->today = $this->getToday();
		$this->group = $this->getGroup($headTaskId);

	}

	function GetToday() {
		$db = &$this->_getDb();

		// Fetch today time
		$rs = $db->query("
			SELECT to_hms(SUM(stop_time - start_time)) AS time
			FROM worktime
			WHERE TO_CHAR(start_time - '7 hours'::interval, 'YYYY-DDD')
				= TO_CHAR('now'::timestamp - '7 hours'::interval, 'YYYY-DDD')
		");

		list($time) = $db->fetch($rs);

		if ($time) {
			$today = $this->ParseTime($time);
		}
		else {
			$today = NULL;
		}

		return $today;
	}

	function GetGroup($headTaskId) {
		$db = &$this->_getDb();

		$headTaskId = $headTaskId  ?  '= '. $headTaskId  :  'IS NULL';

		// Fetch today time
		$rs = $db->query("
			SELECT to_hms(SUM(stop_time - start_time)) AS time
			FROM worktime
				INNER JOIN task ON worktime.task = task.id
			WHERE task.parent $headTaskId
		");

		list($time) = $db->fetch($rs);

		if ($time) {
			$group = $this->ParseTime($time);
		}
		else {
			$group = NULL;
		}

		return $group;
	}

	function ParseTime($time) {
		$cost = '0';

		if ($time) {
			list($hours, $minutes, $seconds) = explode(':', $time);

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
			$time = (int)$hours . ':' . $minutes;
			$cost = round(5*($hours + $minutes/60), 2);
		}
		else {
			$cost = '0';
		}

		return array('time' => $time, 'cost' => $cost);
	}

	function &_getDb() {
		return new DB();
	}
}
?>
