<?php
require_once('../classes/Worktime.php');
require_once('../classes/Task.php');
require_once('../classes/DB.php');
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('simpletest/mock_objects.php');

if (!defined('RUNNER')) {
	define('RUNNER', __FILE__);
}

Mock::generate('DB');
Mock::generatePartial(
	'TaskManager',
	'MockPartTaskManager',
	array('_getDB')
);


class TaskManagerTest extends UnitTestCase {
	function TaskManagerTest() {
		$this->UnitTestCase('TaskManager.php');
	}
	
	function testConstructor() {
	}
	
	function testStop() {
	}
}


if (RUNNER == __FILE__) {
	$test = &new TaskManagerTest();
	$test->run(new HtmlReporter());
}
?>