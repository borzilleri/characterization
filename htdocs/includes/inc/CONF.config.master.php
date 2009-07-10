<?php
/**
 * @author Jonathan Borzilleri
 */

/**
 * Configuration settings
 *
 * @global array $GLOBALS['config']
 * @name $config
 */
$GLOBALS['config'] = array();

/**
 * Database configuration
 */
$config['db'] = array();
$config['db']['engine'] = 'sqlite';
$config['db']['username'] = '';
$config['db']['password'] = '';

// SQLite: Directory path
// MySQL: Hostname
$config['db']['host'] = '';

// SQLite: Database file name
// MySQL: Database name
$config['db']['database'] = '';


$config['dev'] = array();
$config['dev']['debug'] = true;

?>
