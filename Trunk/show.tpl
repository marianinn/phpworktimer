<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="description" content="Script for keeping your working intervals." />
<title>Phpworktimer</title>

<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />


<style type="text/css">
{literal}
body {
	font-family: 'verdana';
	color:#333;
	background-color:white;
}
a {
	color:#00d;
	text-decoration:none;
}
a:hover {
	background-color:#ddd;
}

#Head a {
	font-family: 'courier new';
	padding: 2px;
}
#Head {
	font-family: 'courier new';
	padding: 2px;
	background-color:#eee;
	border:1px grey solid;
	height: 2em;
}

input.add {
	width: 15em;
	font-weight: bold;
	font-family: 'verdana';
	font-size:1em;
	height: 1.5em;
}

td.id{
	background-color:#ddf;
	font-size: .7em;
	width: 2.5em;
	color: #00f
}

td.name {
	background-color:#eef;
	padding-left:1em;
	font-weight: bold;
}
td.name:hover {
	background-color: #ddd;
}
input.input_name {
	display: none;
	font-weight: bold;
	font-family: 'verdana';
	font-size:1em;
	height: 1.5em;
}

td.start {
	background-color:#efe;
}
td.end {
	background-color:#fee;
}
td.start, td.end{
	font-family: 'helvetica';
	font-weight: bold;
	font-size: 1em;
	width: 5em;
}
td.start:hover, td.end:hover {
	background-color:#ddd;
}

td.manage {
	font-size:.6em;
	width: 4em;
	background-color: #ddf;
}
td.manage:hover {
	background-color: #fff;
}

td.worktime_regular {
	border: 1px #77d solid;
}
td.worktime_highlight {
	border: 1px #ddd solid;
	background-color: #fdd;
}
{/literal}
</style>

{if $timer_turned_on}
<link href="favicon.bmp" rel="shortcut icon" />
{/if}

</head>
<body>

<script type="text/javascript">
{literal}
function show(task_id) {
	location.href = 'show.php?parent='+task_id;
}
function start(task_id) {
	location.href = 'start.php?task='+task_id;
}
function end(task_id) {
	location.href = 'end.php?task='+task_id;
}
function edit(task_id, task_name) {
	document.getElementById('span_name_' + task_id).style.display = 'none';
	document.getElementById('input_name_' + task_id).style.display = 'inline';
	document.getElementById('input_name_' + task_id).disabled = false;
	document.getElementById('input_id_' + task_id).disabled = false;
	document.getElementById('input_name_' + task_id).focus();
	document.getElementById('input_name_' + task_id).select();
}
function delete_task(task_id, task_name) {
	if (window.confirm('Do you really want to delete task \'' + task_name + '\'?'))
	{
		location.href = 'delete.php?task='+task_id;
	}
}
{/literal}
</script>


<div align="center">
<table
	width="50%"
	cellspacing="0"
	cellpadding="0"
>
<tr>
<td align="center" id="Head">
	{strip}
	<a href="show.php">Head</a>

	{foreach from=$path item=task}
		/
		<a href="javascript:show({$task.id});">{$task.name}</a>
	{/foreach}
	{/strip}
</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
<td align="center">
	<form action="add.php" method="get" name="input_form">
		<span id="above_input">Add new task</span>
		<input type="hidden" name="id" value="{$parent.id}"/>
		<input type="text" name="name" class="add"/>
	</form>
</td>
</tr>

<form action="edit.php" method="get" name="input_form">
{foreach from=$tasks item=task}
<tr>
<tr><td>&nbsp;</td></tr>
<td class="{if $task.is_working_on}worktime_highlight{else}worktime_regular{/if}">
	<table
		width="100%"
		cellspacing="0"
		cellpadding="0"
		style="height: 2em"
	>
	<tr>
	<td align="center" class="id">
		{$task.id}
	</td>
	<td align="left" class="name" onClick="show({$task.id})">
		<span id="span_name_{$task.id}">{$task.name}</span>
		<input type="hidden" id="input_id_{$task.id}" name="id" value="{$task.id}" disabled="true"/>
		<input type="text" id="input_name_{$task.id}" name="name" value="{$task.name}" class="input_name" disabled="true"/>
	</td>
	<td align="center" class="start" onClick="start({$task.id})">
		Start
	</td>
	<td align="center" class="end" onClick="end({$task.id})">
		End
	</td>
	<td align="right" class="manage">
		<a href="javascript:edit({$task.id}, '{$task.name}')">Edit</a><br/>
		<a href="javascript:delete_task({$task.id}, '{$task.name}')">Delete</a>
	</td>
	</tr>
	</table>
	
	
	<table style="font-weiht: bold;">
	{foreach from=$task.worktimes item=worktime}
	<tr>
	<td style="font-size: .6em;">
		{$worktime.id}.
	</td>
	{if $worktime.end_time}
	<td style="color: #050;" colspan="3">
		{$worktime.start_time}
	</td>
	<td>--</td>
	<td style="color: #800;">
		{$worktime.end_time}
	</td>
	{else}
	<td>
		<b>{$worktime.start_time}</b>
	</td>
	{/if}
	</tr>
	{/foreach}
	</table>
</td>
</tr>
{/foreach}
</form>
</table>
</div>
</body>
</html>
