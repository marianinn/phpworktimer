BEGIN;
COMMIT;


SELECT
	to_hms(SUM(stop_time - start_time)) AS time
	,SUM(compute_cost(stop_time - start_time, task.rate)) AS cost
FROM worktime
	INNER JOIN task ON worktime.task = task.id
WHERE to_day(start_time - '7 hours'::interval)
	= to_day('now'::timestamp - '7 hours'::interval)
;


SELECT
	to_hms(SUM(stop_time - start_time)) AS time
	,SUM(compute_cost(stop_time - start_time, task.rate)) AS cost
FROM worktime
	INNER JOIN task ON worktime.task = task.id
WHERE task.parent $headTaskId
;


UPDATE task
SET
	name = '". $db->escape_string($name) ."',
	rate = " . $db->escape_string($rate) ."
WHERE id = $this->id
;


INSERT INTO worktime(task, start_time)
VALUES($this->id, 'now')
;


SELECT *, stop_time - start_time AS duration
FROM worktime
WHERE id = $worktime_id
;


UPDATE task
SET order_time = 'now'
WHERE id IN(".join(', ', $tasksIds).")
;


DELETE FROM task
WHERE id = $this->id
;


INSERT INTO task(
	parent,
	name,
	rate
)
VALUES(
	". ($this->headTask ? $this->headTask->id : 'NULL') ."
	,'" . $db->escape_string($taskName) . "'
	,". ($this->headTask ? $this->headTask->rate : 1) ."
)
;


SELECT
	task.id,
	name,
	rate,
	to_hms(SUM(stop_time - start_time)) AS total,
	order_time
FROM task
	LEFT JOIN worktime ON task.id = worktime.task
WHERE parent ".($this->headTask ? " = ". $this->headTask->id : "IS NULL")."
GROUP BY task.id, name, rate, order_time
ORDER BY order_time DESC, id DESC
;


SELECT
	worktime.id AS id,
	task,
	start_time,
	stop_time,
	to_hms(stop_time - start_time) AS duration
FROM worktime
	INNER JOIN task ON task.id = worktime.task
WHERE parent ".($this->headTask->id ? " = ". $this->headTask->id : "IS NULL")."
ORDER BY id DESC
;


SELECT task
FROM worktime
WHERE stop_time IS NULL
;


SELECT
	task.id,
	name,
	rate
FROM task
WHERE id = ". $headTaskId ."
;


UPDATE worktime
SET start_time = '$startTime', stop_time = '$stopTime'
WHERE id = $this->id
;


SELECT
	start_time,
	stop_time,
	EXTRACT(day FROM stop_time - start_time)*24
		+ EXTRACT(hour FROM stop_time - start_time)
		|| TO_CHAR(stop_time - start_time, ':MI:SS') AS duration
FROM worktime
WHERE id = $this->id
;


UPDATE worktime
SET stop_time = 'now'
WHERE id = $this->id
;


SELECT
	stop_time,
	stop_time - start_time AS duration
FROM worktime
WHERE id = $this->id
;


DELETE FROM worktime
WHERE id = $this->id
;