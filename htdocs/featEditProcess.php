<?php
include('autohandler.php');

$target_uri = "/";

$char = Doctrine::getTable('Player')->findOneById($_REQUEST['id']);
if( !$char || !$char->exists() ) {
	loadPage($target_uri);
}
$target_uri = "/{$char->id}/skills";

switch($action) {
	case 'save':
		$feat = $char->Feats->get(@$_POST['f_id']);
		$success = $feat->updateFromForm();
		
		if( $success ) {
			$feat->save();
		}
		break;
	case 'delete':
		if( empty($_POST['submit']) || 'delete' != substr($_POST['submit'],0,6) ) {
			break;
		}
		$f_id = substr($_POST['submit'],7);
		$feat = $char->Feats->get($f_id);
		$feat->delete();
		break;
	default:
		break;
}

loadPage($target_uri);
include('footer.php');
?>