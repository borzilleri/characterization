<?php
/**
 * @author Jonathan Borzilleri
 */
 
function errorHandler(
  $errorLevel, $errorString, $errorFile, $errorLine, $errorContext) {
  global $msg;
  $levelString = 'Unknown';
  
  // If our error_reporting value is set to 0 (typically because 
  // the fcn call was invoced with '@', then just exit out and
  // continue processing.
  if( 0 == error_reporting() ) return true;
  
  switch($errorLevel) {
    case E_NOTICE:
    case E_USER_NOTICE:
      $level = Message::NOTICE;
      $levelString = 'Notice';
      break;
    case E_WARNING:
    case E_USER_WARNING:
      $level = Message::WARNING;
      $levelString = 'Warning';
      break;
    case E_ERROR:
    case E_USER_ERROR:
      $level = Message::ERROR;
      $levelString = 'Error';
      break;
    default:
      break;
  }

  if( ini_get('display_errors') ) {
    $msg->add(sprintf('%s in <strong>%s:%d</strong>', 
      $errorString, $errorFile, $errorLine), $level);
  }
    
  if( ini_get('log_errors') ) {
    error_log(sprintf('PHP %s: %s in %s on line %d',
      $levelString, $errorString, $errorFile, $errorLine));
  }
  return true;
}
 
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
