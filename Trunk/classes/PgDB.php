<?php
class PgDB {
	var $dbh;

	function show_last_error() {
		// warning was shown;
	}

	function connect($connection_str) {
		$this->dbh = pg_connect($connection_str);
	}

	function close() {
		return pg_close($this->dbh);
	}

	function query($sql) {
		return pg_query($this->dbh, $sql);
	}

	function num_rows($rs) {
		return pg_num_rows($rs);
	}

	function fetch($rs) {
		return pg_fetch_array($rs);
	}

	function last_insert_id($table_name, $field_name)
	{
		return 'currval(\''. $table_name .'_'. $field_name .'_seq\')';
	}

	function escape_string($rs) {
		return pg_escape_string($rs);
	}
}
?>