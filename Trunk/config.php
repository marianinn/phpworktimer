<?php

$CFG = array();

$CFG['root_dir'] = dirname(__FILE__);

// valid are 'postgresql' and 'mysql'
$CFG['database_type'] = 'postgresql';
#$CFG['database_type'] = 'mysql';

// only for PostgreSQL
$CFG['pg_connection_string'] = 'host=localhost port=5432 user=uzver dbname=phpworktimer password=nlu';

// only for MySQL
$CFG['mysql_server'] = 'localhost';
$CFG['mysql_username'] = 'root';
$CFG['mysql_password'] = '';
$CFG['mysql_db_name'] = 'phpworktimer';

// used php.ini::include_dir
$CFG['smarty_dir'] = 'smarty';

$CFG['smarty_template_dir'] = $CFG['root_dir'] .'/template';

$CFG['smarty_compile_dir'] = $CFG['root_dir'] .'/template/compiled';

$CFG['classes_dir'] = $CFG['root_dir'] .'/classes';



?>