<?php

if (empty($_GET['id']))
{
	$parent_id = 'NULL';
	$uri_parent = '';
}
elseif (preg_match('/^[0-9]+$/', $_GET['id'])) {
	$parent_id = $_GET['id'];
	$uri_parent = "?parent=$parent_id";
}
else {
	$parent_id = NULL;
	$uri_parent = '';
}


if ($parent_id) {
	
	$name = empty($_GET['name']) ? NULL : pg_escape_string($_GET['name']);
	
	pg_query("
		INSERT INTO task(name, parent)
		VALUES('$name', $parent_id)
	");
}


header("Location: show.php$uri_parent");

?>
