DROP TABLE task CASCADE;
DROP SEQUENCE task_id_seq;
DROP TABLE worktime CASCADE;
DROP SEQUENCE worktime_id_seq;
DROP FUNCTION to_hms(INTERVAL);

BEGIN;

CREATE SEQUENCE task_id_seq START 1;
CREATE TABLE task (
	id INT4 NOT NULL DEFAULT nextval('task_id_seq'),
	parent INT4,
	name VARCHAR NOT NULL,
	order_time TIMESTAMP(0) NOT NULL DEFAULT ('now'::TEXT)::TIMESTAMP(0),

	PRIMARY KEY (id),
	FOREIGN KEY (parent) REFERENCES task (id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX task_parent_idx ON task(parent);
CREATE INDEX task_order_time_id_idx ON task(order_time, id);

CREATE SEQUENCE worktime_id_seq;
CREATE TABLE worktime (
	id INT4 NOT NULL DEFAULT nextval('worktime_id_seq'),
	task INT4 NOT NULL,
	start_time TIMESTAMP(0) NOT NULL DEFAULT 'now',
	stop_time TIMESTAMP(0),

	PRIMARY KEY (id),
	FOREIGN KEY (task) REFERENCES task (id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX worktime_task_idx ON worktime(task);


CREATE OR REPLACE FUNCTION to_hms(INTERVAL) RETURNS VARCHAR AS '
	SELECT EXTRACT(day FROM $1)*24 + EXTRACT(hour FROM $1) || TO_CHAR($1, \':MI:SS\');
' LANGUAGE SQL;

COMMIT;
