<?php
require_once('../classes/Worktime.php');
require_once('../classes/DB.php');
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('simpletest/mock_objects.php');

if (!defined('RUNNER')) {
	define('RUNNER', __FILE__);
}

Mock::generate('DB');
Mock::generatePartial(
	'Worktime',
	'MockPartWorktime',
	array('_getDB')
);


class WorktimeTest extends UnitTestCase {
	function WorktimeTest() {
		$this->UnitTestCase('Worktime.php');
	}

	function testConstructor() {
		$assocWorktime = array(
			'id' => '1',
			'task' => '7',
			'start_time' => '2005-06-13 13:42:43',
			'stop_time' => '2005-06-13 13:45:57',
			'duration' => '00:09:20',
		);
		$worktime = &new Worktime($assocWorktime);
		if($this->assertIsA($worktime, 'Worktime')) {
			$this->assertEqual($worktime->taskId, $assocWorktime['task']);
			$this->assertEqual($worktime->startTime, $assocWorktime['start_time']);
			$this->assertEqual($worktime->stopTime, $assocWorktime['stop_time']);
			$this->assertEqual($worktime->duration, $assocWorktime['duration']);
		}
	}

	function testStop() {
 		$assocWorktime = array(
			'id' => '1',
			'task' => '7',
			'start_time' => '2005-06-13 13:42:43',
			'stop_time' => NULL,
			'duration' => NULL,
		);
		$fetchRowRes = array(
			'2005-06-13 13:42:43',
			'02:00:00',
		);

		$mockDB = &new MockDB($this);
		$mockDB->expectMinimumCallCount('query', 2);
		$mockDB->setReturnValue('fetch_row', $fetchRowRes);

		$mockPartWorktime = &new MockPartWorktime($this);

		$mockPartWorktime->setReturnReference('_getDB', $mockDB);
		$mockPartWorktime->Worktime($assocWorktime);

		$this->assertIdentical($mockDB, $mockPartWorktime->_getDB());

		$mockPartWorktime->Stop();

		$mockDB->tally();

		if ($this->assertNotNull($mockPartWorktime->stopTime)) {
			$this->assertEqual($mockPartWorktime->stopTime, $fetchRowRes[0]);
			$this->assertEqual($mockPartWorktime->duration, $fetchRowRes[1]);
		}
	}
}


if (RUNNER == __FILE__) {
	$test = &new WorktimeTest();
	$test->run(new HtmlReporter());
}
?>