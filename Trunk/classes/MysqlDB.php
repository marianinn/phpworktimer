<?php
class MysqlDB {
	var $dbh;

	function show_last_error() {
		v(mysql_error($this->dbh));
	}

	function connect($server, $username, $password, $db_name) {
		$this->dbh = mysql_connect(
			$server,
			$username,
			$password
		); // warning was shown

		return mysql_select_db($db_name);
	}

	function close() {
		mysql_close($this->dbh); // warning was shown
		$this->dbh = NULL;
	}

	function query($sql) {
		return mysql_query($sql, $this->dbh); // warning was shown
	}

	function num_rows($rs) {
		return mysql_num_rows($rs);
	}

	function fetch($rs) {
		return mysql_fetch_array($rs);
	}

	function last_insert_id()
	{
		return mysql_insert_id($this->dbh);
	}

	function escape_string($rs) {
		return mysql_escape_string($rs);
	}
}
?>