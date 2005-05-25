DROP TABLE task CASCADE;
DROP SEQUENCE task_id_seq;
DROP TABLE worktime CASCADE;
DROP SEQUENCE worktime_id_seq;


BEGIN;

CREATE SEQUENCE task_id_seq START 1;
CREATE TABLE task (
    id INT4 NOT NULL DEFAULT nextval('task_id_seq'),
	parent INT4,
    name VARCHAR NOT NULL,
	
    PRIMARY KEY (id),
    FOREIGN KEY (parent) REFERENCES task (id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX task_parent_idx ON task(parent);

CREATE SEQUENCE worktime_id_seq;
CREATE TABLE worktime (
    id INT4 NOT NULL DEFAULT nextval('worktime_id_seq'),
    task INT4 NOT NULL,
    start_time TIMESTAMP(0) NOT NULL DEFAULT 'now',
    end_time TIMESTAMP(0),

    PRIMARY KEY (id),
    FOREIGN KEY (task) REFERENCES task (id) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX worktime_task_idx ON worktime(task);


INSERT INTO task(name) VALUES('Elgraph');
INSERT INTO task(name) VALUES('my own');
INSERT INTO task(name) VALUES('Game');
INSERT INTO task(name, parent) VALUES('New TL', 1);
INSERT INTO task(name, parent) VALUES('T1504', 4);
INSERT INTO task(name, parent) VALUES('T1999', 4);
INSERT INTO task(name, parent) VALUES('T1504', 5);
INSERT INTO task(name, parent) VALUES('T1504', 5);
INSERT INTO task(name, parent) VALUES('comments', 5);
INSERT INTO task(name, parent) VALUES('chat', 3);
INSERT INTO task(name, parent) VALUES('log', 3);

INSERT INTO worktime(task, end_time) VALUES(7, 'now');
INSERT INTO worktime(task, end_time) VALUES(7, 'now');
INSERT INTO worktime(task, end_time) VALUES(7, 'now');
INSERT INTO worktime(task, end_time) VALUES(7, 'now');
INSERT INTO worktime(task, end_time) VALUES(7, 'now');
INSERT INTO worktime(task, end_time) VALUES(8, 'now');
INSERT INTO worktime(task, end_time) VALUES(8, 'now');
INSERT INTO worktime(task, end_time) VALUES(8, NULL);

COMMIT;
