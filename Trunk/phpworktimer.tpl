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
form {
	padding: 0;
	margin: 0;
}
a {
	color:#00d;
	text-decoration:none;
}
a:hover {
	background-color:#ddd;
}

#Head {
	font-family: 'courier new';
	background-color: #fff;
	border: 1px grey solid;
	height: 2em;
}
#Head a {
	font-family: 'courier new';
	margin: 1em;
}

td.name {
	background-color:#eef;
	padding-left:1em;
	font-weight: bold;
}
td.name:hover {
	background-color: #ddd;
}

td.start, td.stop{
	font-family: 'helvetica';
	font-weight: bold;
	font-size: 1em;
	width: 7em;
}
td.start {
	background-color:#efe;
}
td.stop {
	background-color:#fee;
}
td.start:hover, td.stop:hover {
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

td.worktime_start, td.worktime_stop, td.worktime_duration {
//	font-size: 14px;
}
td.worktime_start{
	color: #050;
}
td.worktime_start_active{
	font-weight: bold;
	background-color: #fcc;
}
td.worktime_stop{
	color: #800;
}
td.worktime_manage {
	font-size:.6em;
	width: 4em;
	padding:0;
	line-height: 1em;
	text-align: right;
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

{if $taskManager->isTimerTurnedOn}
<link href="favicon.bmp" rel="shortcut icon" />
{/if}

</head>
<body>
<form action="" name="relocator" method="get">
	<input type="hidden" name="headTaskId" value="{$taskManager->headTaskId}" />
	<input type="hidden" name="action" value="" disabled="1"/>
	<input type="hidden" name="taskId" value="" disabled="1"/>
</form>
<script type="text/javascript">
{if not $taskManager->headTaskId}
document.relocator.headTaskId.disabled = true;
{/if}

{literal}
function Show(headTaskId) {
	document.relocator.headTaskId = headTaskId;
	document.relocator.headTaskId.disabled = fasle;
	document.relocator.submit();
}
function Start(taskId) {
	document.relocator.elements['action'].value = 'start';
	document.relocator.elements['action'].disabled = false;
	document.relocator.elements['taskId'].value = taskId;
	document.relocator.elements['taskId'].disabled = false;
	document.relocator.submit();
}
function Stop() {
	document.relocator.elements['action'].value = 'stop';
	document.relocator.elements['action'].disabled = false;
	document.relocator.submit();
}
function EditTask(taskId) {
	document.getElementById('span_name_'   + taskId).style.display = 'none';
	document.getElementById('hidden_task_' + taskId).disabled = false;
	document.getElementById('input_name_'  + taskId).style.display = 'inline';
	document.getElementById('input_name_'  + taskId).disabled = false;
	document.getElementById('input_name_'  + taskId).focus();
	document.getElementById('input_name_'  + taskId).select();
	document.getElementById('td_name_'     + taskId).onclick = function(){};
}
function DeleteTask(taskId, taskName) {
	if (window.confirm('Do you really want to delete task \'' + taskName + '\'?'))
	{
		document.relocator.elements['action'].value = 'delete';
		document.relocator.elements['action'].disabled = false;
		document.relocator.taskId = taskId;
		document.relocator.submit();
	}
}
/*
function EditWorktime(worktimeId) {
	document.getElementById('span_start_time_'  + worktimeId).style.display = 'none';
	document.getElementById('hidden_worktime_'  + worktimeId).disabled = false;
	document.getElementById('input_start_time_' + worktimeId).style.display = 'inline';
	document.getElementById('input_start_time_' + worktimeId).disabled = false;
	document.getElementById('input_start_time_' + worktimeId).focus();
	document.getElementById('input_start_time_' + worktimeId).select();
	document.tasks_form.action = 'edit_worktime.php';
}                                  
function DeleteWorktime(worktimeId) {
	if (window.confirm('Do you really want to delete worktime id = ' + worktimeId + '?'))
	{
		location.href = 'delete_worktime.php?worktime='+taskId;
	}
}
*/
{/literal}
</script>


<div align="center">
<table
	cellspacing="0"
	cellpadding="0"
>
<tr>
<td align="center" id="Head">
	{strip}
	<a href="show.php">Head</a>

	{foreach from=$taskManager->path item=task}
		/
		<a href="javascript:Show({$task->id});">{$task->name}</a>
	{/foreach}
	{/strip}
</td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
<td align="center">
	<form action="add.php" method="get">
		<span id="above_input">Add new task</span>
		<input type="hidden" name="parent" value="{$parent->id}"/>
		<input type="text" name="name" class="new_task"/>
	</form>
</td>
</tr>

<form action="" method="get" name="tasks_form">
	<input type="hidden" name="action" value="edit" /> 
	<input type="hidden" name="headTaskId" value="{$taskManager->headTaskId}" /> 
{foreach from=$taskManager->tasks item=task}
<tr>
<tr><td>&nbsp;</td></tr>
<td style="{if $task->activeWorktimeId}border: 3px #77d solid;{/if}">
	<table
		width="100%"
		cellspacing="0"
		cellpadding="0"
		style="height: 2em"
	>
	<tr>
	<td
		align="left"
		id="td_name_{$task->id}"
		class="name"
		onClick="Show({$task->id})"
		colspan="{if $taskManager->activeTaskId and not $task->activeWorktimeId}2{else}1{/if}"
	>
		<span id="span_name_{$task->id}">
			{$task->name}
		</span>
		<input
			type="hidden"
			id="hidden_task_{$task->id}"
			name="task"
			value="{$task->id}"
			disabled="true"
		/>
		<input
			type="text"
			id="input_name_{$task->id}"
			name="name"
			value="{$task->name}"
			class="task_name"
			disabled="true"
		/>
	</td>
	{if not $taskManager->activeTaskId}
	<td align="center" class="start" onClick="Start({$task->id})">
		Start
	</td>
	{elseif $task->activeWorktimeId}
	<td align="center" class="stop" onClick="Stop()">
		Stop
	</td>
	{/if}
	<td align="center" class="manage">
		{$task->id}
		<a href="javascript:EditTask({$task->id})">Edit</a><br/>
		<a href="javascript:DeleteTask({$task->id}, '{$task->name}')">Delete</a>
	</td>
	</tr>
	</table>
	
	
	<table style="font-weiht: bold; padding:0px;" cellspacing="0">
	{foreach from=$task->worktimes item=worktime name=worktime}
		<tr style="
		{if $smarty.foreach.worktime.iteration is even}
			background-color: #f3f3f3;
		{else}
			background-color: #ffffff;
		{/if}
		">
		{if not $worktime->stopTime}
			<td colspan="5" class="worktime_start_active">
				<b>{$worktime->startTime}</b>
			</td>
		{else}
			<td class="worktime_start">
				<span id="span_start_time_{$worktime->id}">{$worktime->startTime}</span>
				<input
					type="hidden"
					id="hidden_worktime_{$worktime->id}"
					name="worktime"
					value="{$worktime->id}"
					disabled="false"
				/>
				<input
					type="text"
					id="input_start_time_{$worktime->id}"
					name="startTime"
					value="{$worktime->startTime}"
					class="start_time"
					disabled="true"
				/>
			</td>
			<td>--</td>
			<td class="worktime_stop">
				{$worktime->stopTime}
			</td>
			<td>=</td>
			<td class="worktime_duration">
				{$worktime->duration}
			</td>
		{/if}
		<td align="center" class="worktime_manage">
			{$worktime->id}
			<a href="javascript:EditWorktime({$worktime->id})">Edit</a><br/>
			<a href="javascript:DeleteWorktime({$worktime->id})">Delete</a>
		</td>
		</tr>
	{/foreach}
	{if $task->total}
	<tr style="font-weight: bold;">
	<td colspan="6" align="right">
		Total:
		{$task->total} = ${$task->cost}
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
