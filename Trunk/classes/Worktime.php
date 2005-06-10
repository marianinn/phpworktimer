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
		pg_query("BEGIN");
		pg_query("
			UPDATE worktime
			SET stop_time = 'now'
			WHERE id = $this->id
		");
		$rs = pg_query("
			SELECT
				stop_time,
				stop_time - start_time AS duration
			FROM worktime
			WHERE id = $this->id
		");
		list($this->stopTime, $this->duration) = pg_fetch_row($rs); 
		pg_query("COMMIT");
	}
}

?>