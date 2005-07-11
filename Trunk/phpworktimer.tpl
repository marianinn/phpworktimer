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
	color: #333;
}
table {
	width: 100%;
}
form {
	padding: 0;
	margin: 0;
}
a {
	color: #00d;
	text-decoration: none;
}
a:hover {
	text-decoration: underline;
}

table#tableMain {
	width: 40em;
}

td#tdHead {
	height: 2em;
	border: 1px #555 solid;
	padding: 0 0 0 5em;
	text-align: left;
}
td#tdAddTask {
	padding: .5em 0;
	text-align: center;
}
input#textAddTask {
	width: 30em;
	font-family: 'verdana';
	font-size: 1em;
}
td.task {
	padding: 0 0 1em 0;
}
td.activeTask {
	border: 3px #77d solid;
}
td.taskName {
	background-color: #eef;
	padding-left: 1em;
	font-weight: bold;
}
td.taskName:hover {
	background-color: #ddd;
}
input.textTaskName {
	display: none;
	font-weight: bold;
	font-family: 'verdana';
	font-size: 1em;
	height: 1.5em;
}


td.startTask, td.stopTask {
	width: 7em;
	text-align: center;
	font-weight: bold;
}
td.startTask:hover, td.stopTask:hover {
	background-color: #ddd;
}
td.startTask {
	background-color: #efe;
}
td.stopTask {
	background-color: #fee;
}

td.manageTask {
	font-size: .6em;
	width: 4em;
	background-color: #ddf;
}

tr.worktimeEven {
	background-color: #f3f3f3;
}
tr.worktimeActive{
	background-color: #fcc;
}
td.worktimeStartTimeActive{
	font-weight: bold;
	text-align: center;
}
td.worktimeStartTime{
	color: #050;
	text-align: center;
}
td.worktimeStopTime{
	color: #800;
	text-align: center;
}
td.worktimeDuration {
	text-align: center;
}
td.worktimeManage {
	font-size: .5em;
	width: 4em;
	padding: 0;
	line-height: 1em;
	text-align: right;
}
div.taskTotal {
	text-align: right;
	font-size: 1em;
	font-weight: bold;
}
{/literal}
</style>

{if $taskManager->activeTaskId}
<link href="favicon.bmp" rel="shortcut icon" />
{/if}

</head>
<body>
<script type="text/javascript">
{literal}
/**
 * Submits form with data specified.
 */
function Submit(action, taskId, taskName, headTaskId) {
	if (action != null) {
		document.theForm.action.disabled = false;
		document.theForm.action.value = action;
	}
	if (taskId != null) {
		document.theForm.taskId.disabled = false;
		document.theForm.taskId.value = taskId;
	}
	if (taskName != null) {
		document.theForm.taskName.disabled = false;
		document.theForm.taskName.value = taskName;
	}
	if (headTaskId != null) {
		document.theForm.headTaskId.disabled = false;
		document.theForm.headTaskId.value = headTaskId;
	}

	document.theForm.submit();
}

/**
 * Makes possible to edit task.
 */
function EditTask(taskId) {
	document.getElementById('spanTaskName'   + taskId).style.display = 'none';
	document.getElementById('textTaskName' + taskId).style.display = 'inline';
	document.getElementById('textTaskName' + taskId).focus();
	document.getElementById('textTaskName' + taskId).select();
}

/**
 * Confirms deleting task.
 */
function DeleteTask(taskId, taskName) {
	if (window.confirm('Do you really want to delete task \'' + taskName + '\'?'))
	{
		Submit('delete', taskId);
	}
}
{/literal}
</script>

<form action="" method="get" name="theForm">
	<input type="hidden" name="headTaskId" value="{$taskManager->headTaskId}" />
	<input type="hidden" name="action" disabled="1" />
	<input type="hidden" name="taskId" disabled="1" />
	<input type="hidden" name="taskName" disabled="1" />
	<input type="hidden" name="key" value="{$key}" />
</form>

<div align="center">
	<table id="tableMain" cellspacing="0" cellpadding="0">
		<tr>
			<td id="tdHead">
				<a href="javascript:Submit(null, null, null, '');">Head</a>
			{foreach from=$taskManager->path item=task}
				/ <a href="javascript:Submit(null, null, null, {$task->id});">{$task->name}</a>
			{/foreach}
			</td>
		</tr>
		<tr>
			<td id="tdAddTask">
				<span id="above_input">Add new task</span>
				<input type="text" id="textAddTask"
					onKeyPress="if(event.keyCode==13)
						Submit('add', null, this.value)
					"
				/>
			</td>
		</tr>

	{foreach from=$taskManager->tasks item=task}
		<tr>
			<td class="task {if $task->activeWorktimeId}activeTask{/if}">
				<table cellspacing="0" cellpadding="0">
					<tr>
						<td class="taskName" onClick="Submit(null, null, null, {$task->id})">
							<span id="spanTaskName{$task->id}">
								{$task->name}
							</span>
							<input type="text" id="textTaskName{$task->id}"
								class="textTaskName" value="{$task->name}"
								onKeyPress="if(event.keyCode==13)
									Submit('rename', {$task->id}, this.value)
								"
							/>
						</td>
					{if not $taskManager->activeTaskId}
						<td class="startTask" onClick="Submit('start', {$task->id})">
							Start
						</td>
					{elseif $task->activeWorktimeId}
						<td class="stopTask" onClick="Submit('stop')">
							Stop
						</td>
					{/if}
						<td class="manageTask">
							{$task->id}
							<a href="javascript:EditTask({$task->id})">
								Edit
							</a>
							<br/>
							<a href="javascript:DeleteTask({$task->id}, '{$task->name}')">
								Delete
							</a>
						</td>
					</tr>
				</table>

				<table cellpadding="0" cellspacing="0">
				{foreach from=$task->worktimes item=worktime name=worktime}
					<tr class="
						{if not $worktime->stopTime}worktimeActive{/if}
						{if $smarty.foreach.worktime.iteration is even}worktimeEven{/if}
					">
					{if not $worktime->stopTime}
						<td class="worktimeStartTimeActive">
							{$worktime->startTime}
						</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					{else}
						<td class="worktimeStartTime">
							{$worktime->startTime}
						</td>
						<td>--</td>
						<td class="worktimeStopTime">
							{$worktime->stopTime}
						</td>
						<td>=</td>
						<td class="worktimeDuration">
							{$worktime->duration}
						</td>
					{/if}
						<td align="center" class="worktimeManage">
							{$worktime->id}
							<a href="javascript:EditWorktime({$worktime->id})">Edit</a><br/>
							<a href="javascript:DeleteWorktime({$worktime->id})">Delete</a>
						</td>
					</tr>
				{/foreach}
				</table>
				{if $task->total}
					<div class="taskTotal">
						Total: {$task->total} = ${$task->cost}
					</div>
				{/if}
			</td>
		</tr>
	{/foreach}
	</table>
</div>
</body>
</html>
