<?php
/**
 * $Id: autohandler.php 197 2009-05-29 04:15:54Z john $
 *
 */
$stime = microtime(true);
include(dirname(__FILE__).'/../includes/inc/master.php');
include(dirname(__FILE__).'/../includes/inc/globals.master.php');
include(dirname(__FILE__).'/../includes/inc/session.php');

if( !empty($_REQUEST['id']) ) {
  $char = Doctrine::getTable('Player')->findOneById(@$_REQUEST['id']);
}
else {
  $char = null;
}

/**
 * Unset form cache if:
 * - $form_key is empty
 * - $_SESSION['form_cache'] exists, and does not contain a form_key entry
 * - $_SESSION['form_cache'] exists, contains a form_key entry, and it is
 *   NOT the same as $form_key.
 */
if( empty($form_key) || (!empty($_SESSION['form_cache']) &&
    (!array_key_exists('form_key', $_SESSION['form_cache']) || 
    $form_key != $_SESSION['form_cache']['form_key'])) ) {
  unset($_SESSION['form_cache']);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" media="screen"
      href="<?=MEDIA_URL;?>/css/default.css" />
    <script type="text/javascript" charset="utf-8"
      src="<?=MEDIA_URL;?>/javascript/lib/jquery-1.3.2.min.js"></script>
    <script type="text/javascript" charset="utf-8"
      src="<?=MEDIA_URL;?>/javascript/lib/jquery.qtip-1.0.0-rc3.min.js">
    </script>
    <script type="text/javascript" charset="utf-8"
      src="<?=MEDIA_URL;?>/javascript/lib/json2.js">
    </script>


  <? foreach($css_include as $css): // Page-specific CSS files ?>
    <link rel="stylesheet" type="text/css" media="screen"
      href="<?=SITE_URL;?>/css/<?=$css;?>" />
  <? endforeach; ?>
  
    <script type="text/javascript" charset="utf-8">//<![CDATA[
// Various site 'constants'
var CHAR_ID = <?=$char?$char->id:0;?>;
var SITE_URL = '<?=SITE_URL;?>';
var MEDIA_URL = '<?=MEDIA_URL;?>';
var POWER_PREVIEW_URI = SITE_URL+'/ajax/power.php';

// Icon Constants
var POWER_ICON_DISABLED = MEDIA_URL+'<?=Power::ICON_DISABLED;?>';
var POWER_ICON_USED = MEDIA_URL+'<?=Power::ICON_USED;?>';
var POWER_ICON = MEDIA_URL+'<?=Power::ICON_USABLE;?>';

// Player status constants
var STATUS_UNCONSCIOUS = '<?=Player::STATUS_UNCONSCIOUS;?>';
var STATUS_BLOODIED = '<?=Player::STATUS_BLOODIED;?>';
var STATUS_DEAD = '<?=Player::STATUS_BLOODIED;?>';

// Deprecated
var PROCESS_FAILURE = 'FALSE';
var MESSSAGE_DELIMITER = '|';

/**
 * Retrieve new messages from the session variable.
 * If we find any, call printMessage to display them.
 */
function getMessages() {
	$.ajax({
		url: SITE_URL+'/ajax/messages.php',
		type: "get",
		dataType: 'json',
		success: printMessage,
		error: function(XMLHttpRequest, textStatus, errorThrown) {
		  var data = {
		    level: 'Error',
		    messages: {
		      0: 'An error occured while loading messages: '+errorThrown
		    }
		  };
		  printMessage(data);
	  }
	});
}

/**
 * Display a message to the page.
 *
 * @param array data
 */
function printMessage(data) {
  // Remove all classes from the outer #Messages div,
  // this strips the colored border.
  $('#Messages').removeClass();
  // Next, strip the 'new' class from any divs that have it.
  // This fades any existing messages to grey.
  $('#messageText div.new').removeClass('new');
  
  // Prepend all our messages
  for(var i in data.messages) {
    $('#messageText').prepend(data.messages[i]);
  }
  
  // If we actually HAD any messages, set the appropriate border class,
  // and show the div.
  if( data.messages.length ) {
    $('#Messages').addClass(data.level);
    $('#Messages').fadeIn();
  }
}

/**
 * Clear existing messages from the page.
 */
function clearMessages() {
  $('#Messages').slideUp(function() {
    $('#Messages').removeClass();
    $('#messageText').text('');
  });
}

$(window).load(function() {
  // Global Error display handling
  getMessages();
  $('#ClearMessages').click(function() { clearMessages() });
});
//]]>
    </script>
  <? foreach($js_include as $js): // Page-specific JS files ?>
    <script type="text/javascript" charset="utf-8"
      src="<?=SITE_URL;?>/javascript/<?=$js;?>"></script>
  <? endforeach; ?>

    <title><?=_('Characterization - 4e Characater Manager');?></title>
  </head>
  <body>
<?php include('header.php'); ?>