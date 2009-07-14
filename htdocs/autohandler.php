<?php
/**
 * $Id: autohandler.php 197 2009-05-29 04:15:54Z john $
 *
 */
$stime = microtime(true);
include(dirname(__FILE__).'/../includes/inc/master.php');
include(dirname(__FILE__).'/../includes/inc/globals.master.php');
include(dirname(__FILE__).'/../includes/inc/session.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
      "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" media="screen"
   href="<?=MEDIA_URL;?>/css/default.css" />
  <script type="text/javascript" charset="utf-8"
   src="<?=MEDIA_URL;?>/javascript/jquery/jquery-1.3.2.min.js">
  </script>
  <script type="text/javascript" charset="utf-8"
   src="<?=MEDIA_URL;?>/javascript/jquery/jquery-ui-1.7.1.custom.min.js">
  </script>
  <script type="text/javascript" charset="utf-8"
   src="<?= MEDIA_URL ?>/javascript/jquery/jquery.overlabel.js">
  </script>

<? foreach($css_include as $css): // Page-specific CSS files ?>
  <link rel="stylesheet" type="text/css" media="screen"
   href="<?=SITE_URL;?>/css/<?=$css;?>" />
<? endforeach; ?>

  <script type="text/javascript" charset="utf-8">
//<![CDATA[

// Various site 'constants'
var SITE_URL = '<?=SITE_URL;?>';
var MEDIA_URL = '<?=MEDIA_URL;?>';

$(document).ready(function() {
    //$("label.overlabel").overlabel();

    // Global Error display handling
    $.get("<?=SITE_URL;?>/ajax/messages.php", null,
    function(responseText, textStatus, XMLHttpRequest) {
        var trimResponseText = responseText.replace(/^\s+|\s+$/g, '');

        var data = trimResponseText.split('|');
        var statusText = data[0];
        var message = data[1];

        if( "success" == textStatus ) {
            if( "" == trimResponseText ) {
                $("#messageBlock").hide();
            } else {
                $("#messageBlock").addClass('message' + statusText).append(message).fadeIn();
            }
        } else {
            $("#messageBlock").append('<?=_("An unknown error occured, please try again.");?>');
            $("#messageBlock").fadeIn();
        }
    });
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
