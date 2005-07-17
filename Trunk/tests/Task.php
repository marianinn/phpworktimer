<?php
require_once('../classes/Task.php');
require_once('../classes/Worktime.php');
require_once('../classes/DB.php');
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('simpletest/mock_objects.php');

if (!defined('RUNNER')) {
	define('RUNNER', __FILE__);
}

Mock::generate('DB');
Mock::generate('Worktime');
Mock::generatePartial(
	'Task',
	'MockPartTask',
	array('_getDB')
);
Mock::generatePartial(
	'Task',
	'MockPartTask2',
	array('_getDB', 'Refresh')
);

class TaskTest extends UnitTestCase {
	var $assocTask = array(
		'id' => '1',
		'name' => 'T1504',
		'total' => '00:09:20',
	);
 	var $assocWorktime = array(
		'id' => '1',
		'task' => '7',
		'start_time' => '2005-06-13 13:42:43',
		'stop_time' => NULL,
		'duration' => NULL,
	);
	
	function TaskTest() {
		$this->UnitTestCase('Task.php');
	}
	
	function testConstructor() {
		$task = &new Task($this->assocTask);
		if($this->assertIsA($task, 'Task')) {
			$this->assertEqual($task->id, $this->assocTask['id']);
			$this->assertEqual($task->name, $this->assocTask['name']);
			$this->assertNotNull($task->total, '0:09');
			$this->assertNull($task->activeWorktimeId);
		}
	}
	
	function testAddWorktime() {
		$task = &new Task($this->assocTask);
		$this->assertNull($task->activeWorktimeId);
		$worktime = &new Worktime($this->assocWorktime);
		
		$countWorktimes = count($task->worktimes);
		
		$task->addWorktime($worktime);
		$countWorktimes++;
		
		$this->assertEqual(count($task->worktimes), $countWorktimes);
		$this->assertEqual($task->activeWorktimeId, $worktime->id);
		
		$worktime->id = '123';
		$this->assertNotNull($task->addWorktime($worktime));
		$countWorktimes++;
		
		// we output error worktime anyway
		$this->assertEqual(count($task->worktimes), $countWorktimes);
	}
	
	function testStart() {
		$mockDB = &new MockDB($this);
		$mockDB->expectArgumentsAt(0, 'query', 
			array(new WantedPatternExpectation('/^\\s*INSERT/'))
		);
		$mockDB->expectArgumentsAt(1, 'query',
			array(new WantedPatternExpectation('/^\\s*SELECT/'))
		);
		$mockDB->setReturnValue('fetch_assoc', $this->assocWorktime);
		
		$mockTask = &new MockPartTask($this);
		$mockTask->Task($this->assocTask);
		$mockTask->setReturnReference('_getDB', $mockDB);
		
		$this->assertIdentical($mockDB, $mockTask->_getDB());
		$this->assertNull($mockTask->activeWorktimeId);
		$countWorktimes = count($mockTask->worktimes);
		$mockTask->Start();
		$this->assertEqual($mockTask->activeWorktimeId, $this->assocWorktime['id']);
		$this->assertEqual(count($mockTask->worktimes) - 1, $countWorktimes);
		$mockDB->tally();
		
		$mockDB->expectNever('query');
		$mockTask->Start();		
	}
	
	function testStop() {
		$task = &new Task($this->assocTask);
		$mockWorktime = &new MockWorktime($this);
		$task->worktimes[1] = &$mockWorktime;
		$task->activeWorktimeId = 1;
		
		$mockWorktime->expectOnce('Stop');
		$task->Stop(1);
		$mockWorktime->tally();
		
		$this->assertNull($task->activeWorktimeId);
	}

	function test_CountCost() {
		$task = new Task($this->assocTask);		
		$this->assertEqual($task->total, '0:09');
		
		$task->total = '00:09:31'; 
		$task->_CountCost();
		$this->assertEqual($task->total, '0:10');
		
		$task->total = '00:59:31'; 
		$task->_CountCost();
		$this->assertEqual($task->total, '1:00');

		$task->total = '09:59:31'; 
		$task->_CountCost();
		$this->assertEqual($task->total, '10:00');
		
		$task->total = '71:44:25'; 
		$task->_CountCost();
		$this->assertEqual($task->total, '71:44');
	}
	
	function testRename() {
		$mockDB = &new MockDB($this);
		$mockDB2 = &new MockDB($this);
		
		$mockTask = &new MockPartTask2($this);
		$mockTask->Task($this->assocTask);
		$mockTask->setReturnReference('_getDB', $mockDB);
		$mockTask2 = &new MockPartTask2($this);
		$mockTask2->Task($this->assocTask);
		$mockTask2->setReturnReference('_getDB', $mockDB2);
		
		$mockDB->expectNever('query');
		$mockTask->expectNever('Refresh');
		$mockTask->Rename(NULL);
		$mockTask->Rename(0);
		$mockTask->Rename(' ');
		
		$mockTask2->setReturnReference('_getDB', $mockDB2);
		$mockDB2->expectOnce('query',
			array(new WantedPatternExpectation('/^\\s*UPDATE/'))
		);
		$mockTask2->expectOnce('Refresh');
		$mockTask2->Rename('new name');
		
		$mockDB2->tally();
		$mockTask2->tally();
	}
	
	function testDelete() {
		$mockDB = &new MockDB($this);
		$mockDB->expectOnce('query',
			array(new WantedPatternExpectation('/^\\s*DELETE/'))
		);
		
		$mockTask = &new MockPartTask($this);
		$mockTask->Task($this->assocTask);
		$mockTask->setReturnReference('_getDB', $mockDB);
		
		$mockTask->Delete();
		$mockDB->tally();		
	}
}


if (RUNNER == __FILE__) {
	$test = &new TaskTest();
	$test->run(new HtmlReporter());
}
?>