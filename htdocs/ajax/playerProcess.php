<?
include(dirname(__FILE__).'/../../includes/inc/master.php');
include(dirname(__FILE__).'/../../includes/inc/globals.master.php');

$char = Doctrine::getTable('Player')->findOneById(@$_POST['id']);
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
}

if( $success ) {
  $char->save();
}
else {
 $return = "FALSE"; 
}

echo $return;
?>