<?php
include('autohandler.php');

$target_uri = "/";

$char = Doctrine::getTable('Player')->findOneById($_REQUEST['id']);
if( !$char || !$char->exists() ) {
	loadPage($target_uri);
}
$target_uri = "/{$char->id}/skills";

if( !empty($_POST['submit']) && 'delete' == substr($_POST['submit'],0,6) ) {
	$action = 'delete';
	$s_id = substr($_POST['submit'],7);
}

switch($action) {
	case 'save':
		$skill = $char->Skills->get(@$_REQUEST['name']);
		
		$success = $skill->updateFromForm();
		
		if( $success ) {
			$skill->save();
		}
		break;
	case 'delete':
		$skill = Doctrine::getTable('Skill')->findOneByID($s_id);
		$skill->delete();
		break;
	default:
		break;
}

loadPage($target_uri);
include('footer.php');
?>