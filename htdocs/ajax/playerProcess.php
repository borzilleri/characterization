<?
include(dirname(__FILE__).'/../../includes/inc/master.php');
include(dirname(__FILE__).'/../../includes/inc/globals.master.php');

$char = Doctrine::getTable('Player')->findOneById(@$_POST['id']);
if( !$char || !$char->exists() ) {
  die(false);
}

$success = true;

switch($action) {
  case 'subtractSurge':
    $char->subtractSurge();
    $success = $char->surges_cur;
    break;
  case 'addSurge':
    $char->addSurge();
    $success = $char->surges_cur;
    break;
  case 'togglePower':
    $p = $char->Powers->get($_POST['p_id']);
    // If we can't find our power, invalid op, just die.
    if( !$p->exists() ) die(false);
    
    $success = $p->togglePower();
    break;
}

if( $success ) { 
  $char->save();
}

echo $success;
?>