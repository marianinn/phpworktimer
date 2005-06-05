<?php

if (!empty($_GET['task']) && preg_match('/^[0-9]+$/', $_GET['task']))
{
	$task_id = $_GET['task'];
}

if (!empty($_GET['name'])) {
	$task_name =  pg_escape_string($_GET['name']);
}


if ($task_id && $task_name) {
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
		UPDATE task
		SET name = '$task_name'
		WHERE id = $task_id
	");
}
else {
	exit("Error: empty or bad GET[task] or GET[name]");
}

$uri_parent = isset($uri_parent) ? $uri_parent : '';
header("Location: show.php$uri_parent");

?>