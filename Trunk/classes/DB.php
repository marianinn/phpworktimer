<?php
// Singleton
class DB {
	var $dbh;
	var $connected_str;

	function DB($connection_str = NULL) {
		static $dbh;
		static $connected_str;
		$this->dbh = $dbh;
		$this->connected_str = $connected_str;


		if ($connection_str
			&& $this->dbh
			&& $connection_str != $this->connected_str
		) {
			$this->close();
			$dbh = $this->dbh;
			$connected_str = $this->connected_str;
		}

		if (!$this->dbh) {
			$this->connect($connection_str);
			$dbh = $this->dbh;
			$connected_str = $this->connected_str;
		}


		$this->dbh = $dbh;
		$this->connected_str = $connected_str;
	}

	function connect($connection_str = NULL) {
		global $CFG;

		if (empty($connection_str)) {
			$connection_str = $CFG['pg_connection_string'];
		}
		$this->dbh = pg_connect($connection_str) or die(); // warning was shown
		$this->connected_str = $connection_str;
	}

	function close() {
		pg_close($this->dbh) or die(); // warning was shown
		$this->dbh = NULL;
		$this->connected_str = NULL;
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

	function escape_string($rs) {
		return pg_escape_string($rs);
	}
}
?>