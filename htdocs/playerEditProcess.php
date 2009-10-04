<?php
include('autohandler.php');

$char = null;
if( !empty($_POST['id']) ) {
	$char = Doctrine::getTable('Player')->findOneByID($_POST['id']);
}

$target_uri = '/new';

switch($action) {
	case 'save':
		if( $char ) {
			$target_uri = "/{$char->id}/edit";
		}
		else {
			$char = new Player;
			$target_uri = "/new";
		}
		$success = $char->updateFromForm();

		if( $success ) {
			$char->save();
			$target_uri = "/{$char->id}";
		}
		break;
	case 'delete':
		$char->delete();
		$target_uri = "/";
		break;
}

loadPage($target_uri);
include('footer.php');
?>
