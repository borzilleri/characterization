<?php
/**
 *
 * @author Jonathan Borzilleri
 */
#include(dirname(__FILE__).'/../../includes/inc/session.php');
session_start();
include(dirname(__FILE__).'/../../includes/inc/class.message.php');

$msg = new Message();

$status = $msg->getHighestLevel(true);
$msg_out = "";

foreach($msg->messages() as $m) {
 $msg_out .= $msg->generateHTMLBlock($m['message'],$m['level']);
}

$msg->clear();

echo $status.'|'.$msg_out;
?>