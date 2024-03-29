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
td.taskCaption {
	padding: 0;
}
td.taskName {
	width: 100%;
	padding: 0;
	padding-left: 1em;
	font-weight: bold;
	text-align: left;
	border-bottom: 1px gray solid;
}
td.activeTaskName, td.activeTaskRate {
	background-color: #fcc;
}
input.textTaskName {
	display: none;
	font-weight: bold;
	font-family: 'verdana';
	font-size: 1em;
	width: 100%;
}
td.taskRate {
	padding-left: 1em;
	font-weight: bold;
	border-bottom: 1px gray solid;
}
input.textTaskRate {
	display: none;
	width: 2em;
	font-weight: bold;
	font-family: 'verdana';
	font-size: 1em;
	text-align: right;
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
	cursor: default;
}
td.showTask:hover, td.startTask:hover, td.stopTask:hover {
	background-color: #ddd;
	cursor: hand;
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
	text-align: center;
}
td.worktimeDivider {
	text-align: center;
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

var editedTaskId = null;
var editedTaskOldName = null;
var editedTaskOldRate = null;
var editedWorktimeId = null;
var editedWorktimeOldStartTime = null;
var editedWorktimeOldStopTime = null;

/**
Sends request to create new task.
*/
function AddTask(taskName) {
	document.theForm.action.disabled = false;
	document.theForm.action.value = 'addTask';
	document.theForm.taskName.disabled = false;
	document.theForm.taskName.value = taskName;
	document.theForm.submit();
}

/**
Changes current headTask.
*/
function ShowTask(taskId) {
	document.theForm.key.disabled = true;
	if (taskId != null) {
		document.theForm.headTaskId.value = taskId;
	}
	else {
		document.theForm.headTaskId.disabled = true;
	}
	document.theForm.submit();
}
/**
Sends request to edit task.
*/
function EditTask(taskId) {

	var newTaskName = document.getElementById('textTaskName' + taskId).value;
	var newTaskRate = document.getElementById('textTaskRate' + taskId).value;

	document.theForm.action.disabled = false;
	document.theForm.action.value = 'editTask';
	document.theForm.taskId.disabled = false;
	document.theForm.taskId.value = taskId;
	document.theForm.taskName.disabled = false;
	document.theForm.taskName.value = newTaskName;
	document.theForm.taskRate.disabled = false;
	document.theForm.taskRate.value = newTaskRate;
	document.theForm.submit();
}

/**
Sends request to delete task after confirmation.
*/
function DeleteTask(taskId) {
	if (window.confirm('Are you serious?'))
	{
		document.theForm.action.disabled = false;
		document.theForm.action.value = 'deleteTask';
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
	document.theForm.action.value = 'startTask';
	document.theForm.taskId.disabled = false;
	document.theForm.taskId.value = taskId;
	document.theForm.submit();
}

/**
Sends request to delete task.
*/
function Stop() {
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
function ToggleEditTask(taskId) {
	document.getElementById('spanTaskName' + taskId).style.display = 'none';
	document.getElementById('textTaskName' + taskId).style.display = 'inline';
	document.getElementById('spanTaskRate' + taskId).style.display = 'none';
	document.getElementById('textTaskRate' + taskId).style.display = 'inline';

	editedTaskId = taskId;
	editedTaskOldName = document.getElementById('textTaskName' + taskId).value;
	editedTaskOldRate = document.getElementById('textTaskRate' + taskId).value;
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
	if (editedTaskId != null) {
		document.getElementById('textTaskName' + editedTaskId).value = editedTaskOldName;
		document.getElementById('textTaskRate' + editedTaskId).value = editedTaskOldRate;

		document.getElementById('spanTaskName' + editedTaskId).style.display = 'inline';
		document.getElementById('textTaskName' + editedTaskId).style.display = 'none';
		document.getElementById('spanTaskRate' + editedTaskId).style.display = 'inline';
		document.getElementById('textTaskRate' + editedTaskId).style.display = 'none';
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

function Myonkeypress(e) {
	if (e.keyCode == 27) {
		CancelEditing();
	}
}

window.onkeypress = Myonkeypress;
{/literal}
</script>

<form action="" method="get" name="theForm">
	<input type="hidden" name="key" value="{$key}" />
	<input type="hidden" name="headTaskId" value="{$taskManager->headTask->id}" />
	<input type="hidden" name="action" disabled="disabled" />
	<input type="hidden" name="taskId" disabled="disabled" />
	<input type="hidden" name="taskName" disabled="disabled" />
	<input type="hidden" name="taskRate" disabled="disabled" />
	<input type="hidden" name="worktimeId" disabled="disabled" />
	<input type="hidden" name="worktimeStartTime" disabled="disabled" />
	<input type="hidden" name="worktimeStopTime" disabled="disabled" />
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
					onkeypress="if(event.keyCode==13) AddTask(this.value)"
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
						<td class="taskCaption">
							<table>
								<tr>
									<td class="taskName {if $task->isActive}activeTaskName{/if}">
										<span id="spanTaskName{$task->id}">
											{$task->name}
										</span>
										<input type="text" id="textTaskName{$task->id}"
											class="textTaskName" value="{$task->name}"
											onkeypress="if(event.keyCode==13) EditTask({$task->id})"
										/>
									</td>
									<td class="taskRate {if $task->isActive}activeTaskRate{/if}">
										<span id="spanTaskRate{$task->id}">
											{$task->rate}
										</span>
										<input type="text" id="textTaskRate{$task->id}"
											class="textTaskRate" value="{$task->rate}"
											onkeypress="if(event.keyCode==13) EditTask({$task->id})"
										/>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class="taskCaption">
							<table>
								<tr>
									<td class="taskId">
										{$task->id}
									</td>
									<td class="manageTask">
										<a href="javascript:ToggleEditTask({$task->id})">
											Edit</a><br/>
										<a href="javascript:DeleteTask({$task->id})">
											Delete
										</a>
									</td>
									<td class="showTask" onclick="ShowTask({$task->id})">
										Show
									</td>
								{if not $taskManager->activeTaskId}
									<td class="startTask" onclick="StartTask({$task->id})">
										Start
									</td>
								{elseif $taskManager->activeTaskId eq $task->id}
									<td class="stopTask" onclick="Stop()">
										Stop
									</td>
								{else}
									<td class="actionEmpty"></td>
								{/if}
								</tr>
							</table>
						</td>
					</tr>
				</table>

			{if $task->worktimes}
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
							{$worktime->startTime|date_format:"%Y-%m-%d %H:%M:%S"}
						</td>
						<td></td>
						<td></td>
					{else}
						<td class="worktimeStartTime">
							<span id="spanWorktimeStartTime{$worktime->id}">
								{$worktime->startTime|date_format:"%Y-%m-%d %H:%M:%S"}
							</span>
							<input type="text" id="textWorktimeStartTime{$worktime->id}"
								class="worktimeStartTime"
								value="{$worktime->startTime|date_format:"%Y-%m-%d %H:%M:%S"}"
								onkeypress="if(event.keyCode==13) EditWorktime({$worktime->id})"
							/>
						</td>
						<td class="worktimeDivider">--</td>
						<td class="worktimeStopTime">
							<span id="spanWorktimeStopTime{$worktime->id}">
								{$worktime->stopTime|date_format:"%Y-%m-%d %H:%M:%S"}
							</span>
							<input type="text" id="textWorktimeStopTime{$worktime->id}"
								class="worktimeStopTime"
								value="{$worktime->stopTime|date_format:"%Y-%m-%d %H:%M:%S"}"
								onkeypress="if(event.keyCode==13) EditWorktime({$worktime->id})"
							/>
						</td>
						<td class="worktimeDivider">=</td>
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
