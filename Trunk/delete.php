<?php

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

	pg_query("
		DELETE FROM task
		WHERE id = $task_id
	");
}

header("Location: show.php$uri_parent");

?>