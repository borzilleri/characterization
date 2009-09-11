<?
include(dirname(__FILE__).'/../../includes/inc/master.php');
include(dirname(__FILE__).'/../../includes/inc/globals.master.php');
include(dirname(__FILE__).'/../../includes/inc/session.php');

$success = true;
$return = true;

$char = Doctrine::getTable('Player')->findOneById(@$_REQUEST['id']);
if( !$char || !$char->exists() ) {
  die(false);
}


$p = $char->Powers->get($_REQUEST['p_id']);
if( $p->exists() ) {
  $return = $p->getPowerBoxDisplay();  
}
else {
  $msg->add("Unknown power with id '{$_POST['p_id']}'.", Message::ERROR);
  $return = "FALSE";
}


$status = $msg->getHighestLevel(true);
$msg_out = "";
foreach($msg->messages() as $m) {
 $msg_out .= $msg->generateHTMLBlock($m['message'],$m['level']);
}
$msg->clear();

if( $msg_out ) {
 echo "{$return}|{$status}|{$msg_out}";
}
else {
  echo $return;
}
?>