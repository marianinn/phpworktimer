<?php
class SQLiteDB {
	var $dbh;

	function connect($db_filename) {
		$this->dbh = sqlite_open($db_filename); // warning was shown
	}

	function close() {
		sqlite_close($this->dbh); // warning was shown
		$this->dbh = NULL;
	}

	function query($sql) {
		return sqlite_query($this->dbh, $sql); // warning was shown
	}

	function num_rows($rs) {
		return sqlite_num_rows($rs);
	}

	function fetch($rs) {
		return sqlite_fetch_array($rs);
	}

	function last_insert_id()
	{
		return sqlite_last_insert_rowid($this->dbh);
	}

	function escape_string($rs) {
		return sqlite_escape_string($rs);
	}
}
?>