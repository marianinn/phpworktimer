<?php
define('RUNNER', __FILE__);
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('classes.php');
require_once('DB.php');


$test = &new GroupTest('All tests');
$test->addTestCase(new DBTest);
$test->addTestCase(new ClassesTest);
$test->run(new HtmlReporter());
?>