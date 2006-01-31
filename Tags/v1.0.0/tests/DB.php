<?php
if (!defined('RUNNER')) {
	define('RUNNER', __FILE__);
	require('simpletest/unit_tester.php');
	require('simpletest/reporter.php');
}

class DbTest extends UnitTestCase {
	function testConnecting2realdb() {
		$DB_CONNECTION_STRING = 'host=localhost port=5432 user=uzver dbname=phpworktimer password=nlu';
		$DBH = pg_connect($DB_CONNECTION_STRING);
		$OK_status = is_resource($DBH) && pg_connection_status($DBH) == PGSQL_CONNECTION_OK;
		if ($this->assertTrue($OK_status, 'Connect to \'phpworktimer\' base')) {
			$this->assertTrue(pg_close($DBH));
		}
	}

	function testConnecting2testdb() {
		$DB_CONNECTION_STRING = 'host=localhost port=5432 user=uzver dbname=test password=nlu';
		$DBH = pg_connect($DB_CONNECTION_STRING);
		$OK_status = is_resource($DBH) && pg_connection_status($DBH) == PGSQL_CONNECTION_OK;
		if ($this->assertTrue($OK_status, 'Connect to \'test\' base')) {
			$this->assertTrue(pg_close($DBH));
		}
	}

	function testSQLFile() {
		$file_name = '../phpworktimer.sql';
		if ($this->assertTrue(file_exists($file_name))) {
			$DB_CONNECTION_STRING = 'host=localhost port=5432 user=uzver dbname=test password=nlu';
			$DBH = pg_connect($DB_CONNECTION_STRING);
			$sql = explode(';', file_get_contents($file_name));
			unset($sql[count($sql) - 1]);

			foreach ($sql as $statement) {
				@pg_query($DBH, $statement);
			}

			$rs = pg_query($DBH, 'SELECT COUNT(*) FROM task');
			list($count) = pg_fetch_row($rs);
			$this->assertTrue($count == 0, 'Count tasks=' . $count . ', must be == 0');

			$rs = pg_query($DBH, 'SELECT COUNT(*) FROM worktime');
			list($count) = pg_fetch_row($rs);
			$this->assertTrue($count == 0, 'Count worktimes = ' . $count . ', must be == 0');
		}
	}
}


if (RUNNER == __FILE__) {
	$test = &new TestDb();
	$test->run(new HtmlReporter());
}
?>