<?
include(dirname(__FILE__).'/../../includes/inc/master.php');
include(dirname(__FILE__).'/../../includes/inc/globals.master.php');
include(dirname(__FILE__).'/../../includes/inc/session.php');

$char = Doctrine::getTable('Player')->findOneById(@$_REQUEST['id']);
if( !$char || !$char->exists() ) {
  die(false);
}

$success = true;

$data = empty($_POST['data'])?array():json_decode($_POST['data'],true);
$result = array();

switch($action) {
  case 'addSurge':
    $success = $char->addSurge();
    $result['surges_cur'] = $char->surges_cur;
    break;
  case 'subtractSurge':
    $success = $char->subtractSurge();
    $result['surges_cur'] = $char->surges_cur;
    break;
  case 'spendSurge':
    $success = $char->useSurge((int)@$data['surge_bonus']);
    $result['surges_cur'] = $char->surges_cur;
    $result['health_cur'] = $char->health_cur;    
    break;
  case 'damage':
    $success = $char->takeDamage((int)@$data['health']);
    $result['health_cur'] = $char->health_cur;
    $result['health_tmp'] = $char->health_tmp;
    break;
  case 'tempHealth':
    $success = $char->addTempHealth((int)@$data['health']);
    $result['health_tmp'] = $char->health_tmp;
    break;
  case 'addActionPoint':
    $success = $char->addActionPoint();
    $result['action_points'] = $char->action_points;
    break;
  case 'subtractActionPoint':
    $success = $char->subtractActionPoint();
    $result['action_points'] = $char->action_points;
    break;
  case 'addMagicItemUse':
    $success = $char->addMagicItemUse();
    $result['magic_item_uses'] = $char->magic_item_uses;
    break;
  case 'subtractMagicItemUse':
    $success = $char->subtractMagicItemUse();
    $result['magic_item_uses'] = $char->magic_item_uses;
    break;
  case 'shortRest':
    $success = $char->shortRest();
    $result['health_tmp'] = $char->health_tmp;
    break;
  case 'extendedRest':
    $success = $char->extendedRest();
    $result['health_cur'] = $char->health_cur;
    $result['health_tmp'] = $char->health_tmp;
    $result['surges_cur'] = $char->surges_cur;
    $result['action_points'] = $char->action_points;
    $result['magic_item_uses'] = $char->magic_item_uses;
    break;
  case 'updateNotes':
    $char->notes = trim($data['notes']);
    $result['player_notes'] = $char->notes;
    break;
//------------------------------------------------------------------------------
  case 'usePower':
    $p = $char->Powers->get($_POST['p_id']);
    // If we can't find the power, die.
    if( !$p->exists() ) die(false);
    $success = $p->usePower();
    break;
  case 'refreshPower':
    $p = $char->Powers->get($_POST['p_id']);
    // If we can't find the power, die.
    if( !$p->exists() ) die(false);
    $success = $p->refresh();
    break;
}

if( $success ) {
  $char->save();
}


$status = $msg->getHighestLevel(true);
$msg_out = "";
$messages = $msg->messages();
$msg->clear();

if( is_array($messages) && !empty($messages) ) {
  $result['errors'] = array();
  $result['errors']['level'] = $status;
  foreach($messages as $m) {    
    $result['errors'][] = $msg->generateHTMLBlock($m['message'],$m['level']);
  }
}

echo json_encode($result);
?>