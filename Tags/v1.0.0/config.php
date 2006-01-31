<?php

$CFG = array();

$CFG['root_dir'] = '/usr/htdocs/phpworktimer';

$CFG['smarty_dir'] = 'smarty'; // used php.ini::include_dir

$CFG['smarty_template_dir'] = $CFG['root_dir'] .'/template';

$CFG['smarty_compile_dir'] = $CFG['root_dir'] .'/template/compiled';

$CFG['classes_dir'] = $CFG['root_dir'] .'/classes';

$CFG['pg_connection_string'] = 'host=localhost port=5432 user=uzver dbname=phpworktimer password=nlu';


?>