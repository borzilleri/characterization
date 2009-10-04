<?php
include('autohandler.php');

$target_uri = "/";

$char = Doctrine::getTable('Player')->findOneById($_REQUEST['id']);
if( !$char || !$char->exists() ) {
	loadPage($target_uri);
}

switch($action) {
	case 'save':
		$power = $char->Powers->get(@$_REQUEST['p_id']);
		$target_uri = "/{$char->id}/power".
			($power->exists()?"/{$power->id}/edit":"/new");
	
		$success = $power->updateFromForm();
	
		if( $success ) {
			$power->save();
			$target_uri = "/{$char->id}/power";
		}
		break;
	default:
		$target_uri = "/{$char->id}/power/new";
		break;
}

loadPage($target_uri);
include('footer.php');
?>