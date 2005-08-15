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
	border-collapse: collapse;
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

div.errorMsg {
	color: red;
	text-align: center;
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
td#tdStatistics {
	font-weight: bold;
	text-align: center;
}

td.task {
	padding: 0 0 1em 0;
}
table.taskCaption {
	border: 1px #777 solid;
}
td.taskName {
	padding-left: 1em;
	font-weight: bold;
	text-align: left;
	border-bottom: 1px gray solid;
}
input.textTaskName {
	display: none;
	font-weight: bold;
	font-family: 'verdana';
	font-size: 1em;
	height: 1.5em;
}

td.taskId {
	font-size: .6em;
	padding: 0 1em;
	width: 2em;
}
td.manageTask {
	font-size: .6em;
	background-color: #ddf;
	padding: 0 1em;
}
td.showTask, td.startTask, td.stopTask, td.actionEmpty {
	text-align: center;
	font-weight: bold;
	width: 16em;
}
td.showTask:hover, td.startTask:hover, td.stopTask:hover {
	background-color: #ddd;
}
td.showTask {
	background-color: #eef;
}
td.startTask {
	background-color: #efe;
}
td.stopTask {
	background-color: #fee;
}

tr.worktime {
	height: 1.5em;
	border: 1px gray dotted;
}
td.worktimeManage {
	font-size: .5em;
	width: 5em;
	padding: 0;
	line-height: 1em;
	text-align: center;
}
td.worktimeId {
	font-size: .6em;
	width: 3em;
	padding: 0;
}
tr.worktimeEven {
	background-color: #f3f3f3;
}
tr.worktimeActive {
	background-color: #fcc;
}
td.worktimeStartTimeActive {
	font-weight: bold;
	text-align: center;
}
td.worktimeStartTime {
	color: #050;
	text-align: center;
	width: 12em;
}
td.worktimeStopTime {
	color: #800;
	text-align: center;
	width: 12em;
}
td.worktimeDuration {
	text-align: center;
	width: 6em;
}
input.worktimeStartTime, input.worktimeStopTime {
	display: none;
	font-family: 'verdana';
	font-size: 1em;
}
input.worktimeStartTime {
	color: #050;
}
input.worktimeStopTime {
	color: #800;
}
div.taskTotal {
	text-align: right;
	font-size: 1em;
	font-weight: bold;
}

td.emptyMsg {
	padding-top: 1em;
	font-weight: bold;
	text-align: center;
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

var renamedTaskId = null;
var renamedTaskOldName = null;
var editedWorktimeId = null;
var editedWorktimeOldStartTime = null;
var editedWorktimeOldStopTime = null;

/**
Sends request to create new task.
*/
function AddTask(taskName) {
	document.theForm.action.disabled = false;
	document.theForm.action.value = 'add';
	document.theForm.taskName.disabled = false;
	document.theForm.taskName.value = taskName;
	document.theForm.submit();
}

/**
Changes current headTaskId.
*/
function ShowTask(taskId) {
	if (taskId != null) {
		document.theForm.headTaskId.value = taskId;
	}
	else {
		document.theForm.headTaskId.disabled = true;
	}
	document.theForm.submit();
}
/**
Sends request to rename task.
*/
function RenameTask(taskId, newTaskName) {
	document.theForm.action.disabled = false;
	document.theForm.action.value = 'rename';
	document.theForm.taskId.disabled = false;
	document.theForm.taskId.value = taskId;
	document.theForm.taskName.disabled = false;
	document.theForm.taskName.value = newTaskName;
	document.theForm.submit();
}

/**
Sends request to delete task after confirmation.
*/
function DeleteTask(taskId) {
	if (window.confirm('Are you serious?'))
	{
		document.theForm.action.disabled = false;
		document.theForm.action.value = 'delete';
		document.theForm.taskId.disabled = false;
		document.theForm.taskId.value = taskId;
		document.theForm.submit();
	}
}

/**
Sends request to delete task.
*/
function StartTask(taskId) {
	document.theForm.action.disabled = false;
	document.theForm.action.value = 'start';
	document.theForm.taskId.disabled = false;
	document.theForm.taskId.value = taskId;
	document.theForm.submit();
}

/**
Sends request to delete task.
*/
function StopTask() {
	document.theForm.action.disabled = false;
	document.theForm.action.value = 'stop';
	document.theForm.submit();
}

/**
Sends request to edit worktime.
*/
function EditWorktime(worktimeId) {
	document.theForm.action.disabled = false;
	document.theForm.action.value = 'editWorktime';
	document.theForm.worktimeId.disabled = false;
	document.theForm.worktimeId.value = worktimeId;
	document.theForm.worktimeStartTime.disabled = false;
	document.theForm.worktimeStartTime.value = document.getElementById('textWorktimeStartTime'+worktimeId).value;
	document.theForm.worktimeStopTime.disabled = false;
	document.theForm.worktimeStopTime.value = document.getElementById('textWorktimeStopTime'+worktimeId).value;
	document.theForm.submit();
}


/**
Sends request to delete worktime after confirmation.
*/
function DeleteWorktime(worktimeId) {
	if (window.confirm('Are you serious?'))
	{
		document.theForm.action.disabled = false;
		document.theForm.action.value = 'deleteWorktime';
		document.theForm.worktimeId.disabled = false;
		document.theForm.worktimeId.value = worktimeId;
		document.theForm.submit();
	}
}

/**
 * Makes possible to edit task.
 */
function ToggleRenameTask(taskId) {
	document.getElementById('spanTaskName' + taskId).style.display = 'none';
	document.getElementById('textTaskName' + taskId).style.display = 'inline';

	renamedTaskId = taskId;
	renamedTaskOldName = document.getElementById('textTaskName' + taskId).value;
}

/**
 * Makes possible to edit worktime.
 */
function ToggleEditWorktime(worktimeId) {
	document.getElementById('spanWorktimeStartTime' + worktimeId).style.display = 'none';
	document.getElementById('textWorktimeStartTime' + worktimeId).style.display = 'inline';
	document.getElementById('spanWorktimeStopTime' + worktimeId).style.display = 'none';
	document.getElementById('textWorktimeStopTime' + worktimeId).style.display = 'inline';

	editedWorktimeId = worktimeId;
	editedWorktimeOldStartTime = document.getElementById('textWorktimeStartTime' + worktimeId).value;
	editedWorktimeOldStopTime = document.getElementById('textWorktimeStopTime' + worktimeId).value;
}


/**
Cancels all editing toggles.
*/
function CancelEditing() {
	if (renamedTaskId != null) {
		document.getElementById('textTaskName' + renamedTaskId).value = renamedTaskOldName;

		document.getElementById('spanTaskName' + renamedTaskId).style.display = 'inline';
		document.getElementById('textTaskName' + renamedTaskId).style.display = 'none';
	}

	if (editedWorktimeId != null) {
		document.getElementById('textWorktimeStartTime' + editedWorktimeId).value = editedWorktimeOldStartTime;
		document.getElementById('textWorktimeStopTime' + editedWorktimeId).value = editedWorktimeOldStopTime;
		document.getElementById('spanWorktimeStartTime' + editedWorktimeId).style.display = 'inline';
		document.getElementById('textWorktimeStartTime' + editedWorktimeId).style.display = 'none';
		document.getElementById('spanWorktimeStopTime' + editedWorktimeId).style.display = 'inline';
		document.getElementById('textWorktimeStopTime' + editedWorktimeId).style.display = 'none';
	}
}

function MyOnKeyPress(e) {
	if (e.keyCode == 27) {
		CancelEditing();
	}
}

window.onkeypress = MyOnKeyPress;
{/literal}
</script>

<form action="" method="get" name="theForm">
	<input type="hidden" name="key" value="{$key}" />
	<input type="hidden" name="headTaskId" value="{$taskManager->headTaskId}" />
	<input type="hidden" name="action" disabled="1" />
	<input type="hidden" name="taskId" disabled="1" />
	<input type="hidden" name="taskName" disabled="1" />
	<input type="hidden" name="worktimeId" disabled="1" />
	<input type="hidden" name="worktimeStartTime" disabled="1" />
	<input type="hidden" name="worktimeStopTime" disabled="1" />
</form>

{if $errorMsg}
	<div class="errorMsg">{$errorMsg}</div>
{/if}

<div align="center">
	<table id="tableMain" cellspacing="0" cellpadding="0">
		<tr>
			<td id="tdHead">
				<a href="javascript:ShowTask(null);">Head</a>
			{foreach from=$taskManager->path item=task}
				/ <a href="javascript:ShowTask({$task->id});">{$task->name}</a>
			{/foreach}
			</td>
		</tr>
		<tr>
			<td id="tdAddTask">
				<span id="above_input">Add new task</span>
				<input type="text" id="textAddTask"
					onKeyPress="if(event.keyCode==13) AddTask(this.value)"
				/>
			</td>
		</tr>
		<tr>
			<td id="tdStatistics">
			{if $statistics->today}
				<span style="margin: 0 2em;">
					Today: {$statistics->today.time} = {$statistics->today.cost}
				</span>
			{/if}
			{if $statistics->group}
				<span style="margin: 0 2em;">
					This Group: {$statistics->group.time} = {$statistics->group.cost}
				</span>
			{/if}
			</td>
		</tr>

	{foreach from=$taskManager->tasks item=task}
		<tr>
			<td class="task">
				<table class="taskCaption">
					<tr>
						<td class="taskName" colspan="4">
							<span id="spanTaskName{$task->id}">
								{$task->name}
							</span>
							<input type="text" id="textTaskName{$task->id}"
								class="textTaskName" value="{$task->name}"
								onKeyPress="if(event.keyCode==13) RenameTask({$task->id}, this.value)"
							/>
						</td>
					</tr>
					<tr>
						<td class="taskId">
							{$task->id}
						</td>
						<td class="manageTask">
							<a href="javascript:ToggleRenameTask({$task->id})">
								Rename</a><br/>
							<a href="javascript:DeleteTask({$task->id})">
								Delete
							</a>
						</td>
						<td class="showTask" onClick="ShowTask({$task->id})">
							Show
						</td>
					{if not $taskManager->activeTaskId}
						<td class="startTask" onClick="StartTask({$task->id})">
							Start
						</td>
					{elseif $task->activeWorktimeId}
						<td class="stopTask" onClick="StopTask()">
							Stop
						</td>
					{else}
						<td class="actionEmpty"></td>
					{/if}
					</tr>
				</table>

				<table cellpadding="0" cellspacing="0">
				{foreach from=$task->worktimes item=worktime name=worktime}
					<tr class="worktime
						{if not $worktime->stopTime}worktimeActive{/if}
						{if $smarty.foreach.worktime.iteration is even}worktimeEven{/if}
					">
						<td class="worktimeId">
							{$worktime->id}
						</td>
						<td class="worktimeManage">
							<a href="javascript:ToggleEditWorktime({$worktime->id})">Edit</a><br/>
							<a href="javascript:DeleteWorktime({$worktime->id})">Delete</a>
						</td>
					{if not $worktime->stopTime}
						<td colspan="3" class="worktimeStartTimeActive">
							{$worktime->startTime}
						</td>
						<td></td>
						<td></td>
					{else}
						<td class="worktimeStartTime">
							<span id="spanWorktimeStartTime{$worktime->id}">{$worktime->startTime}</span>
							<input type="text" id="textWorktimeStartTime{$worktime->id}"
								class="worktimeStartTime" value="{$worktime->startTime}"
								onKeyPress="if(event.keyCode==13) EditWorktime({$worktime->id})"
							/>
						</td>
						<td>--</td>
						<td class="worktimeStopTime">
							<span id="spanWorktimeStopTime{$worktime->id}">{$worktime->stopTime}</span>
							<input type="text" id="textWorktimeStopTime{$worktime->id}"
								class="worktimeStopTime" value="{$worktime->stopTime}"
								onKeyPress="if(event.keyCode==13) EditWorktime({$worktime->id})"
							/>
						</td>
						<td>=</td>
						<td class="worktimeDuration">
							{$worktime->duration}
						</td>
					{/if}
					</tr>
				{/foreach}
				</table>
				{if $task->total}
					<div class="taskTotal">
						Total: {$task->total} = {$task->cost}
					</div>
				{/if}
			</td>
		</tr>
	{foreachelse}
		<tr>
			<td class="emptyMsg">This task group is empty.</td>
		</tr>
	{/foreach}
	</table>
</div>
</body>
</html>
