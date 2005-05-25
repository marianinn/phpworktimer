<?php

if (!empty($_GET['id']) && preg_match('/^[0-9]+$/', $_GET['id']))
{
	$task_id = $_GET['id'];
}
else {
	$task_id = NULL;
}
$name = empty($_GET['name']) ? NULL : pg_escape_string($_GET['name']); 


if ($task_id) {
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
		SET name = '$name'
		WHERE id = $task_id
	");
}


$uri_parent = isset($uri_parent) ? $uri_parent : '';

header("Location: show.php$uri_parent");

?>
