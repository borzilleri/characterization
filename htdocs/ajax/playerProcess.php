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
		$result['refreshPowers'] = false;
		break;
	case 'extendedRest':
		$success = $char->extendedRest();
		$result['health_cur'] = $char->health_cur;
		$result['health_tmp'] = $char->health_tmp;
		$result['surges_cur'] = $char->surges_cur;
		$result['action_points'] = $char->action_points;
		$result['magic_item_uses'] = $char->magic_item_uses;
		$result['refreshPowers'] = true;
		break;
	case 'updateNotes':
		$char->notes = trim($data['notes']);
		$result['player_notes'] = $char->notes;
		break;
	case 'togglePower':
		$surges = $char->surges_cur;
		$mi_use = $char->magic_item_uses;
		$p = $char->Powers->get($data['p_id']);
		if( !$p->exists() ) {
			$msg->add('Unable to find Power "'.$data['p_id'].'".', Message::ERROR);
		}
		else {
			$success = $p->togglePower();
		}
		$result['power'] = array('pID' => $p->id, 'status' => $p->getUsageStatus());
		if( $mi_use != $char->magic_item_uses ) 
			$result['magic_item_uses'] = $char->magic_item_uses;
		if( $surges != $char->surges_cur )
			$result['surges_cur'] = $char->surges_cur;
		break;
}

if( $success ) {
	$char->save();
}


$result['errors'] = $msg->getOutput(false);
$msg->clear();

print_r(json_encode($result));
?>