<?php
/**
 * @author Jonathan Borzilleri
 */
 
/**
 * Dynamically load a class file on demand.
 *
 * @param string $class The name of the class to load.
 * @return bool
 */
function autoLoad($class) {
    $filename = 'class.'.strtolower($class).'.php';
    $file = dirname(__FILE__).'/'.$filename;
    if( !file_exists($file) ) {
        return false;
    }
    include($file);
    return true;
}

/**
 * Uses the Location header to redirect the user to a new URI.
 *
 * @param string $path The URI path to load.
 * @param bool $include_query whether to include the current QUERY_STRING.
 */
function loadPage($path, $include_query = false) {
    $http = !empty($_SERVER['HTTPS']) ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    // TODO Clean up query?
    $query = $_SERVER['QUERY_STRING'];
    header("Location: {$http}://{$host}{$path}".($include_query?"?{$query}":""));
    exit;
}

/**
 * localeDateTime
 *
 * Returns a locale-aware timestamp from a date and/or time string which
 * is parsable by strtotime().
 *
 * @param string $date
 * @return string
 */
function localeDateTime($date) {
    $time = strtotime($date);

    // alternative $time % (60*60*24) == 0
    if ( date('H:i:s', $time) == '00:00:00' )
        return strftime('%x', $time);
    else
        return strftime('%x %X', $time);
}

/**
 *
 * @global $config
 * @return string
 */
function generateDSN() {
  global $config;
  $dsn = '';
  
  switch($config['db']['engine']) {
    case 'sqlite':
      $dsn = 'sqlite:';
      break;
    case 'mysql':
    default:
      $dsn = 'mysql://';
      break;
  }
  
  if( !empty($config['db']['username']) ) {
    $dsn .= $config['db']['username'];
    
    if( !empty($config['db']['password']) ) {
      $dsn .= ":{$config['db']['password']}";
    }
    $dsn .= '@';
  }
  
  $dsn .= "{$config['db']['host']}/{$config['db']['database']}";
  return $dsn;
}

?>
