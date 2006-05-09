<?php

not working yet

include('config.php');
include($CFG['classes_dir'] . '/DBFactory.php');

$db = new DBFactory();
$db = $db->GetDB();

$db->query('
	CREATE TABLE task(
		id INTEGER NOT NULL PRIMARY KEY,
		parent INTEGER,
		name VARCHAR NOT NULL,
		order_time TIMESTAMP
	)
');

?>