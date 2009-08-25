<?
include(dirname(__FILE__).'/../../includes/inc/master.php');
include(dirname(__FILE__).'/../../includes/inc/globals.master.php');
include(dirname(__FILE__).'/../../includes/inc/session.php');

$char = Doctrine::getTable('Player')->findOneById(@$_REQUEST['id']);
if( !$char || !$char->exists() ) {
  die(false);
}

$success = true;
$return = true;

switch($action) {
  case 'subtractSurge':
    $success = $char->subtractSurge();
    $return = $char->surges_cur;
    break;
  case 'addSurge':
    $success = $char->addSurge();
    $return = $char->surges_cur;
    break;
  case 'spendSurge':
    $success = $char->useSurge((int)@$_POST['surge_bonus']);
    $return = $char->surges_cur.':'.$char->health_cur;
    break;
  case 'addActionPoint':
    $success = $char->addActionPoint();
    $return = $char->action_points;
    break;
  case 'subtractActionPoint':
    $success = $char->subtractActionPoint();
    $return = $char->action_points;
    break;
  case 'togglePower':
    $p = $char->Powers->get($_POST['p_id']);
    // If we can't find our power, invalid op, just die.
    if( !$p->exists() ) die(false);    
    $success = $p->togglePower();
    break;
  case 'rest':
    $success = $char->doRest(@$_POST['rest_type']);
    $return = $char->action_points;
    break;
  case 'damage':
    $success = $char->takeDamage((int)@$_POST['health']);
    $return = $char->health_cur.':'.$char->health_tmp;
    break;
  case 'tempHealth':
    $success = $char->addTempHealth((int)@$_POST['health']);
    $return = $char->health_tmp;
    break;
  case 'updateNotes':
    $char->notes = trim($_POST['notes']);
    $return = $char->notes;
}

if( $success ) {
  $char->save();
}
else {
  $return = "FALSE"; 
}

$status = $msg->getHighestLevel(true);
$msg_out = "";
$messages = $msg->messages();
$msg->clear();

if( is_array($messages) ) {
  foreach($messages as $m) {
    $msg_out .= $msg->generateHTMLBlock($m['message'],$m['level']);
  }
}

if( $msg_out ) {
  echo "{$return}|{$status}|{$msg_out}";
}
else {
  echo $return;
}
?>