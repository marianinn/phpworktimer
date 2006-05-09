<?php
define('RUNNER', __FILE__);
require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');
require_once('classes.php');


$test = &new GroupTest('All tests');
$test->addTestCase(new ClassesTest);
$test->run(new HtmlReporter());
?>