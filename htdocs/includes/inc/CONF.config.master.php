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
$config['db']['engine'] = 'mysql';
$config['db']['host'] = 'localhost';
$config['db']['username'] = '';
$config['db']['password'] = '';
$config['db']['database'] = 'characterization';

$config['dev'] = array();
$config['dev']['debug'] = true;

?>
