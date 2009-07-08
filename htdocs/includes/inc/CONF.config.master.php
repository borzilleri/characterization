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

/**
 * For SQLite Databases, 'host' should be the directory path and 
 * 'database' should be the actual db file name
 */
$config['db']['mysql']['host'] = 'localhost';
$config['db']['mysql']['database'] = 'characterization';
$config['db']['mysql']['username'] = '';
$config['db']['mysql']['password'] = '';

$config['dev'] = array();
$config['dev']['debug'] = true;

?>
