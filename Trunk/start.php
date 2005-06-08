<?php
require('index.php');

if (!empty($_GET['task']) && preg_match('/^[0-9]+$/', $_GET['task']))
{
	$task_id = $_GET['task'];
}

$uri_parent = '';

if (!empty($task_id)) {

	$rs = pg_query("
		SELECT parent
		FROM task
		WHERE id = $task_id
	");
	list($parent_id) = pg_fetch_row($rs);
	if ($parent_id) {
		$uri_parent = "?parent=$parent_id";
	}

	$rs = pg_query("
		SELECT NULL
		FROM worktime
		WHERE stop_time IS NULL
	");

	if (!pg_num_rows($rs)) {
		pg_query("
			INSERT INTO worktime(task, start_time)
			VALUES($task_id, 'now')
		");
	}
}
else {
	exit("Error: empty or bad GET[task]");
}

header("Location: show.php$uri_parent");

?>