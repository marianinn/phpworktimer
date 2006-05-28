<?php
class Worktime {
	var $id;
	var $taskId;
	var $startTime;
	var $stopTime;
	var $duration;
	var $task;

	function Worktime($assocWorktime) {
		$this->id = $assocWorktime['id'];
		$this->taskId = $assocWorktime['task'];
		$this->startTime = $assocWorktime['start_time'];
		$this->stopTime = $assocWorktime['stop_time'];
		$this->duration = $assocWorktime['duration'];
	}

	function edit($startTime, $stopTime) {
		if (!preg_match('/^20[0-9]{2}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/',
			$startTime)
		) {
			return 'Exception: bad startTime';
		}
		if (!preg_match('/^20[0-9]{2}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/',
			$stopTime)
		) {
			return 'Exception: bad stopTime';
		}

		if ($startTime >= $stopTime)
		{
			return 'Exception: stopTime is not greater than startTime';
		}


		$db = &$this->_getDb();

		// Update worktime
		$rs = $db->query("
			UPDATE worktime
			SET start_time = '$startTime', stop_time = '$stopTime'
			WHERE id = $this->id
		");

		// Select updated values
		$rs = $db->query("
			SELECT
				start_time,
				stop_time,
				to_hms(stop_time - start_time) AS duration
			FROM worktime
			WHERE id = $this->id
		");

		list($this->startTime, $this->stopTime, $this->duration) = $db->fetch($rs);
	}

	function stop() {
		if ($this->stopTime) {
			return 'Exception: already stopped';
		}

		$db = &$this->_getDB();

		$db->query("BEGIN");
		$now = date('Y-m-d H:i:s O');

		$db->query("
			UPDATE worktime
			SET stop_time = '". $now ."'
			WHERE id = ". $this->id ."
		");

		// Refresh $this
		$rs = $db->query("
			SELECT
				stop_time,
				stop_time - start_time AS duration
			FROM worktime
			WHERE id = ". $this->id ."
		");
		list($this->stopTime, $this->duration) = $db->fetch($rs);
		$db->query("COMMIT");
	}

	function delete() {
		$db = &$this->_getDB();

		$db->query("
			DELETE FROM worktime
			WHERE id = ". $this->id ."
		");
	}

	function &_getDB() {
		return new DB();
	}
}

?>