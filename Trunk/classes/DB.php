<?php
// Delegates its duties to the $_db field.
class DB {
	var $_db;// static

	function DB() {
		static $db = NULL;
		$this->_db = &$db;

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
			$this->_db->connect($CFG['pg_connection_string']);
		}
		elseif ($CFG['database_type'] == 'mysql')
		{
			include($CFG['classes_dir'] .'/MysqlDB.php');
			$this->_db = new MysqlDB();
			$result = $this->_db->connect(
				$CFG['mysql_server'],
				$CFG['mysql_username'],
				$CFG['mysql_password'],
				$CFG['mysql_db_name']
			);

			if (!$result) {
				echo('Refused to select database "'. $CFG['mysql_db_name'] .'"');
				v($result);
				exit;
			}
		}
		else
		{
			exit('Error: bad CFG[database_type]');
		}
	}

	function close() {
		$this->_db->close();
		$this->_db = NULL;
	}

	function query($sql) {
		$rs = $this->_db->query($sql);
		if (!is_resource($rs)) {
			$this->_db->show_last_error();
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