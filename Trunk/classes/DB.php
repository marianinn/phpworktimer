<?php
// Singleton
class DB {
	var $dbh;
	var $def_conn_str = 'host=localhost port=5432 user=uzver dbname=phpworktimer password=nlu';
	
	function DB($conn_str = NULL) {
		static $dbh;
		if (!$dbh) {
			$this->connect($conn_str);
			$dbh = $this->dbh;
		}
		$this->dbh = $dbh;
	}
	
	function connect($conn_str = NULL) {
		$conn_str = $conn_str ? $conn_str : $this->def_conn_str;
		$this->dbh = pg_connect($conn_str) or die(); // warning was shown
	}
	
	function query($sql) {
		return pg_query($this->dbh, $sql);
	}
	
	function num_rows($rs) {
		return pg_num_rows($rs);
	}
	
	function fetch_row($rs) {
		return pg_fetch_row($rs);
	}
	
	function fetch_assoc($rs) {
		return pg_fetch_assoc($rs);
	}
}
?>