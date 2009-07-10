<?php
$base_dir = dirname(dirname(dirname(__FILE__))).'/';

// Include your Doctrine configuration/setup here, your connections, models, etc.
$no_db_access = true;
require($base_dir.'includes/inc/master.php');

// Configure Doctrine Cli
// Normally these are arguments to the cli tasks but if they are set here the arguments will be auto-filled and are not required for you to enter them.

$config = array('data_fixtures_path'  =>  $base_dir.'setup/sql/fixtures',
                'models_path'         =>  $base_dir.'includes/models',
                'migrations_path'     =>  $base_dir.'setup/sql/migrations',
                'sql_path'            =>  $base_dir.'setup/sql/fixtures',
                'yaml_schema_path'    =>  $base_dir.'setup/sql/schema.yml');
$cli = new Doctrine_Cli($config);
$cli->run($_SERVER['argv']);
?>
