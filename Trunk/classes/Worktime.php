<?php
class Worktime {
	var $id;
	var $task;
	var $start_time;
	var $end_time;
	
	var $_dbc;
	
	function Worktime($id, $task, $start_time, $end_time) {
		$this->id = $id; 
		$this->task = $task; 
		$this->start_time = $start_time; 
		$this->end_time = $end_time; 
	}
}

?>