<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="Script for keeping your working intervals." />
<title>phpworktimer</title>

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

td.name {
	background-color:#eef;
	padding-left:1em;
	font-weight: bold;
}
td.name:hover {
	background-color: #ddd;
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

td.worktime_manage {
	font-size:.6em;
	width: 4em;
	padding:0;
}

input.new_task {
	width: 15em;
	font-family: 'verdana';
	font-size:1em;
}
input.task_name {
	display: none;
	font-weight: bold;
	font-family: 'verdana';
	font-size:1em;
	height: 1.5em;
}
input.start_time {
	display: none;
	font-family: 'verdana';
	font-size:1em;
	color: #050;
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
function edit_task(task_id) {
	document.getElementById('span_name_'   + task_id).style.display = 'none';
	document.getElementById('hidden_task_' + task_id).disabled = false;
	document.getElementById('input_name_'  + task_id).style.display = 'inline';
	document.getElementById('input_name_'  + task_id).disabled = false;
	document.getElementById('input_name_'  + task_id).focus();
	document.getElementById('input_name_'  + task_id).select();
	document.getElementById('td_name_'     + task_id).onclick = function(){};
}
function delete_task(task_id, task_name) {
	if (window.confirm('Do you really want to delete task \'' + task_name + '\'?'))
	{
		location.href = 'delete.php?task='+task_id;
	}
}
function edit_worktime(worktime_id) {
	document.getElementById('span_start_time_'  + worktime_id).style.display = 'none';
	document.getElementById('hidden_worktime_'  + worktime_id).disabled = false;
	document.getElementById('input_start_time_' + worktime_id).style.display = 'inline';
	document.getElementById('input_start_time_' + worktime_id).disabled = false;
	document.getElementById('input_start_time_' + worktime_id).focus();
	document.getElementById('input_start_time_' + worktime_id).select();
	document.tasks_form.action = 'edit_worktime.php';
}                                  
function delete_worktime(worktime_id) {
	if (window.confirm('Do you really want to delete worktime id = ' + worktime_id + '?'))
	{
		location.href = 'delete_worktime.php?worktime='+task_id;
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
	<form action="add.php" method="get">
		<span id="above_input">Add new task</span>
		<input type="hidden" name="parent" value="{$parent.id}"/>
		<input type="text" name="name" class="new_task"/>
	</form>
</td>
</tr>

<form action="edit.php" method="get" name="tasks_form">
{foreach from=$tasks item=task}
<tr>
<tr><td>&nbsp;</td></tr>
<td style="{if $task.is_working_on}border: 3px #77d solid;{/if}">
	<table
		width="100%"
		cellspacing="0"
		cellpadding="0"
		style="height: 2em"
	>
	<tr>
	<td align="left" id="td_name_{$task.id}" class="name" onClick="show({$task.id})">
		<span id="span_name_{$task.id}">{$task.name}</span>
		<input
			type="hidden"
			id="hidden_task_{$task.id}"
			name="task"
			value="{$task.id}"
			disabled="true"
		/>
		<input
			type="text"
			id="input_name_{$task.id}"
			name="name"
			value="{$task.name}"
			class="task_name"
			disabled="true"
		/>
	</td>
	<td align="center" class="start" onClick="start({$task.id})">
		Start
	</td>
	<td align="center" class="end" onClick="end({$task.id})">
		End
	</td>
	<td align="center" class="manage">
		{$task.id}
		<a href="javascript:edit_task({$task.id})">Edit</a><br/>
		<a href="javascript:delete_task({$task.id}, '{$task.name}')">Delete</a>
	</td>
	</tr>
	</table>
	
	
	<table style="font-weiht: bold; padding:0px;" cellspacing="0">
	{foreach from=$task.worktimes item=worktime name=worktime}
	<tr style="
	{if $smarty.foreach.worktime.iteration is even}
		background-color: #f5f5f5;
	{else}
		background-color: #ffffff;
	{/if}
	">
	{if $worktime.end_time}
	<td style="color: #050; width:11em;">
		<span id="span_start_time_{$worktime.id}">{$worktime.start_time}</span>
		<input
			type="hidden"
			id="hidden_worktime_{$worktime.id}"
			name="worktime"
			value="{$worktime.id}"
			disabled="false"
		/>
		<input
			type="text"
			id="input_start_time_{$worktime.id}"
			name="start_time"
			value="{$worktime.start_time}"
			class="start_time"
			disabled="true"
		/>
	</td>
	<td>-</td>
	<td style="color: #800; width:11em;">
		{$worktime.end_time}
	</td>
	<td>:</td>
	<td>
		{$worktime.duration}
	</td>
	{else}
	<td colspan="5">
		<b>{$worktime.start_time}</b>
	</td>
	{/if}
	<td align="center" class="worktime_manage">
		{$worktime.id}
		<a href="javascript:edit_worktime({$worktime.id})">Edit</a><br/>
		<a href="javascript:delete_worktime({$worktime.id})">Delete</a>
	</td>
	</tr>
	{/foreach}
	{if $task.total}
	<tr style="font-weight: bold;">
	<td colspan="6" align="right">
		Total:
		{$task.total} = ${$task.cost}
	</td>
	</tr>
	{/if}
	</table>
</td>
</tr>
{/foreach}
</form>
</table>
</div>
</body>
</html>
