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
			SELECT
				to_hms(SUM(stop_time - start_time)) AS time
				,SUM(compute_cost(stop_time - start_time, task.rate)) AS cost
			FROM worktime
				INNER JOIN task ON worktime.task = task.id
			WHERE to_day(start_time - '7 hours'::interval)
				= to_day('now'::timestamp - '7 hours'::interval)
		");

		list($time, $cost) = $db->fetch($rs);

		$today = NULL;
		if ($time) {
			$today['time'] = $this->ParseTime($time);
			$today['cost'] = $cost;
		}

		return $today;
	}

	function GetGroup($headTaskId) {
		$db = &$this->_getDb();

		$headTaskId = $headTaskId  ?  '= '. $headTaskId  :  'IS NULL';

		// Fetch today time
		$rs = $db->query("
			SELECT
				to_hms(SUM(stop_time - start_time)) AS time
				,SUM(compute_cost(stop_time - start_time, task.rate)) AS cost
			FROM worktime
				INNER JOIN task ON worktime.task = task.id
			WHERE task.parent $headTaskId
		");

		list($time, $cost) = $db->fetch($rs);

		$group = NULL;
		if ($time) {
			$group['time'] = $this->ParseTime($time);
			$group['cost'] = $cost;
		}

		return $group;
	}

	/**
	 * Rounds given $time string to 'HHH:ii'.
	 * Input:
	 * 	$time like '23:59:34'
	 * Output: string like '24:00'
	 */
	function ParseTime($time) {
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
		}

		return $time;
	}

	function &_getDb() {
		return new DB();
	}
}
?>
