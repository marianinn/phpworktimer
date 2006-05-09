<?php

error_reporting(E_ALL);

require('phpworktimer.php');
require('config.php');

$phpworktimer = new phpworktimer();
$phpworktimer->main();

?>