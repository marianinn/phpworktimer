<?php
class Worktime {
	var $id;
	var $taskId;
	var $startTime;
	var $stopTime;
	var $duration;
	
	function Worktime($assocWorktime) {
		$this->id = $assocWorktime['id']; 
		$this->taskId = $assocWorktime['task']; 
		$this->startTime = $assocWorktime['start_time']; 
		$this->stopTime = $assocWorktime['stop_time']; 
		$this->duration = $assocWorktime['duration']; 
	}
	
	function Stop() {
		$db = &$this->_getDB();

		$db->query("BEGIN");
		$db->query("
			UPDATE worktime
			SET stop_time = 'now'
			WHERE id = $this->id
		");
		
		// Refresh $this
		$rs = $db->query("
			SELECT
				stop_time,
				stop_time - start_time AS duration
			FROM worktime
			WHERE id = $this->id
		");
		list($this->stopTime, $this->duration) = $db->fetch_row($rs); 
		$db->query("COMMIT");
	}
	
	function &_getDB() {
		return new DB;
	}
}

?>