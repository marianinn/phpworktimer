<?php
// Delegates its duties to the $_db field.
class DB {
	var $_db;// static
	var $_dbh;// static

	function DB() {
		static $db = NULL;
		static $dbh = NULL;
		$this->_db = &$db;
		$this->_dbh = &$dbh;

		if ($this->_db === NULL)
		{
			$this->_fillDb();
		}
	}

	function _fillDb() {
		global $CFG;

		if ($CFG['database_type'] == 'postgresql')
		{
			include($CFG['classes_dir'] .'/PgDB.php');
			$this->_db = new PgDB();
			$connection_str = $CFG['pg_connection_string'];
		}
		elseif ($CFG['database_type'] == 'sqlite')
		{
			include($CFG['classes_dir'] .'/SQLiteDB.php');
			$this->_db = new SQLiteDB();
			$connection_str = $CFG['sqlite_db_filename'];
		}
		else
		{
			exit('Error: bad CFG[database_type]');
		}
		$this->connect($connection_str);
	}

	function connect($connection_str) {
		$this->_dbh = $this->_db->connect($connection_str);
	}

	function close() {
		$this->_db->close($this->_dbh);
		$this->_dbh = NULL;
	}

	function query($sql) {
		$rs = $this->_db->query($this->_dbh, $sql);
		if (!is_resource($rs)) {
			v();
			v($sql);
			exit;
		}
		else {
			return $rs;
		}
	}

	function num_rows($rs) {
		return $this->_db->num_rows($rs);
	}

	function fetch($rs) {
		return $this->_db->fetch($rs);
	}

	function last_insert_id($table_name, $field_name = 'id')
	{
		return $this->_db->last_insert_id($table_name, $field_name = 'id');
	}

	function escape_string($rs) {
		return $this->_db->escape_string($rs);
	}
}
?>