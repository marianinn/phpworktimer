BEGIN;

-- Is used to compare two dates only by day
-- ( to_day(date1) = to_day(date2) ) <=> ( date1 is the same day as date2 )
CREATE OR REPLACE FUNCTION to_day(TIMESTAMP(0) WITH TIME ZONE) RETURNS INTEGER AS '
	SELECT (EXTRACT(YEAR FROM $1)::INTEGER * 512 + EXTRACT(DOY FROM $1)::INTEGER)
' LANGUAGE SQL IMMUTABLE RETURNS NULL ON NULL INPUT;

-- Parses an interval to varchar like '123:24:56'
CREATE OR REPLACE FUNCTION to_hms(INTERVAL) RETURNS VARCHAR AS '
	SELECT EXTRACT(day FROM $1)*24 + EXTRACT(hour FROM $1) || TO_CHAR($1, \':MI:SS\');
' LANGUAGE SQL IMMUTABLE RETURNS NULL ON NULL INPUT;

-- Multiplies hours from given interval on given "hourly rate"
CREATE OR REPLACE FUNCTION compute_cost(INTERVAL, NUMERIC) RETURNS NUMERIC AS '
	SELECT ROUND(
		$2 * (
			EXTRACT(day FROM $1)*24
			+ EXTRACT(hour FROM $1)
			+ EXTRACT(minute FROM $1)/60
			+ EXTRACT(second FROM $1)/3600
		)::NUMERIC
		,2
	);
' LANGUAGE SQL IMMUTABLE RETURNS NULL ON NULL INPUT;


CREATE SEQUENCE task_id_seq START 1;
CREATE TABLE task (
	id INT4 NOT NULL DEFAULT nextval('task_id_seq'),
	parent INT4,
	rate NUMERIC NOT NULL,
	name VARCHAR NOT NULL,
	order_time TIMESTAMP(0) WITH TIME ZONE NOT NULL
		DEFAULT ('now'::TEXT)::TIMESTAMP(0) WITH TIME ZONE,

	PRIMARY KEY (id),
	FOREIGN KEY (parent) REFERENCES task (id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX task_parent_idx ON task(parent);
CREATE INDEX task_order_time_id_idx ON task(order_time, id);

CREATE SEQUENCE worktime_id_seq;
CREATE TABLE worktime (
	id INT4 NOT NULL DEFAULT nextval('worktime_id_seq'),
	task INT4 NOT NULL,
	start_time TIMESTAMP(0) WITH TIME ZONE NOT NULL
		DEFAULT ('now'::TEXT)::TIMESTAMP(0) WITH TIME ZONE,
	stop_time TIMESTAMP(0) WITH TIME ZONE,

	PRIMARY KEY (id),
	FOREIGN KEY (task) REFERENCES task (id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX worktime_task_idx ON worktime(task);
CREATE INDEX worktime_start_time_idx ON worktime(to_day(start_time));


COMMIT;
