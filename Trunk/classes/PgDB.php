<?php
class PgDB {
	function connect($connection_str) {
		return pg_connect($connection_str); // warning was shown
	}

	function close($dbh) {
		return pg_close($dbh); // warning was shown
	}

	function query($dbh, $sql) {
		return pg_query($dbh, $sql); // warning was shown
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