<?php
include('autohandler.php');

$char = null;
if( !empty($_POST['id']) ) {
  $char = Doctrine::getTable('Player')->findOneByID($_POST['id']);
}

switch($action) {
  case 'save':
    if( $char ) {
      $redir_url = "/{$char->id}/edit";
    }
    else {
      $char = new Player;
      $redir_url = "/new";
    }
    $success = $char->updateFromForm();
  
    if( $success ) {
      $char->save();
      loadPage("/{$char->id}");
    }
    else {
      loadPage($redir_url);
    }
    break;
  case 'delete':
    $char->delete();
    loadPage("/");
    break;
}

loadPage('/new');
include('footer.php');
?>
