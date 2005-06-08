<?php
require('index.php');

if (empty($_GET['parent']))
{
	$parent_id = 'NULL';
	$uri_parent = '';
}
elseif (preg_match('/^[0-9]+$/', $_GET['parent'])) {
	$parent_id = $_GET['parent'];
	$uri_parent = "?parent=$parent_id";
}
else {
	$parent_id = NULL;
	$uri_parent = '';
}

if (!empty($_GET['name'])) {
	$task_name =  pg_escape_string($_GET['name']);
}


if ($parent_id && $task_name) {
	pg_query("
		INSERT INTO task(name, parent)
		VALUES('$task_name', $parent_id)
	");
}
else {
	exit("Error: empty or bad GET[parent] or GET[name]");
}


header("Location: show.php$uri_parent");

?>