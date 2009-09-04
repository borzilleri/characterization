<?php
/**
 *
 * @author Jonathan Borzilleri
 */
include(dirname(__FILE__).'/../../includes/inc/session.php');
include(dirname(__FILE__).'/../../includes/inc/class.message.php');

$msg = new Message();
$out = $msg->getOutput();
$msg->clear();

print_r($out);
?>