<?php
/**
 * @author     Jonathan Borzilleri
 */

/**
 * String constant, base URI for the site.
 */
define('SITE_URL', 'http://'.$_SERVER['HTTP_HOST']);

/**
 * Base URI for Media files.
 */
define('MEDIA_URL', SITE_URL);

/**
 * Messenger object
 * @global object $GLOBALS['messenger']
 * @name $messenger
 */
#$GLOBALS['messenger'] = new Message();

/**
 * Javascript include array.
 *
 * An array of javascript file paths that will be included on the page.
 * This array should be populated on each page BEFORE including autohandler.php,
 * the definition here exists strictly for documentation and ensuring the 
 * foreach loop in autohandler.php does not break.
 *
 * Elements of this array should be paths relative to /includes/javascript/ 
 *
 * @global array $GLOBALS['js_include']
 * @name $js_include
 */
$GLOBALS['js_include'] = (empty($js_include)||!is_array($js_include))?array():$js_include;

/**
 * CSS Include array.
 *
 * An array of CSS file paths that will be included on the page.
 * This array should be populated on each page BEFORE including autohandler.php,
 * the definition here exists strictly for documentation and ensuring the 
 * foreach loop in autohandler.php does not break.
 *
 * Elements of this array should be paths relative to /includes/css/
 *
 * @global array $GLOBALS['css_include']
 * @name $css_include
 */
$GLOBALS['css_include'] = (empty($css_include)||!is_array($css_include))?array():$css_include;

/**
 * Page action string.
 *
 * This pulls the 'action' variable from the $_REQUEST array. It's used to
 * indicate what process the user is attempting on a given page.
 * 
 * @global string $GLOBALS['action']
 * @name $action
 */
$GLOBALS['action'] = empty($_REQUEST['action']) ? null : $_REQUEST['action'];
?>
