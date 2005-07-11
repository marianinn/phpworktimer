<?php
if (!defined('RUNNER')) {
	define('RUNNER', __FILE__);
}

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('Worktime.php');
require_once('Task.php');

class ClassesTest extends GroupTest {
	function ClassesTest() {
		$this->GroupTest('Class tests');
		$DB_CONNECTION_STRING = 'host=localhost port=5432 user=uzver dbname=test password=nlu';
		$DBH = pg_connect($DB_CONNECTION_STRING);
		
		$this->addTestCase(new WorktimeTest());
		$this->addTestCase(new TaskTest());
	}
}

if (RUNNER == __FILE__) {
	$test = &new ClassesTest();
	$test->run(new HtmlReporter());
}
	
?>