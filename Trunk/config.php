<?php

$CFG = array();

$CFG['root_dir'] = dirname(__FILE__);

// valid are 'postgresql' and 'sqlite'
$CFG['database_type'] = 'postgresql';

// only for PostgreSQL
$CFG['pg_connection_string'] = 'host=localhost port=5432 user=uzver dbname=phpworktimer password=nlu';

// only for SQLite
$CFG['sqlite_db_filename'] = 'phpworktimer.db';

// used php.ini::include_dir
$CFG['smarty_dir'] = 'smarty';

$CFG['smarty_template_dir'] = $CFG['root_dir'] .'/template';

$CFG['smarty_compile_dir'] = $CFG['root_dir'] .'/template/compiled';

$CFG['classes_dir'] = $CFG['root_dir'] .'/classes';



?>