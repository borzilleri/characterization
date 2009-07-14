<?php
include('autohandler.php');

$char = Doctrine::getTable('Player')->findOneById($_REQUEST['id']);
if( !$char || !$char->exists() ) {
  loadPage('/');
}

if( 'save' == $action ) {
  $power = $char->Powers->get(@$_REQUEST['p_id']);
  $redir_url = "/{$char->id}/power".
    ($power->exists()?"/{$power->id}/edit":"/new");
  
  $success = $power->updateFromForm();
  
  if( $success ) {
    $power->save();
    loadPage("/{$char->id}/power");
  }
  else {
    loadPage($redir_url);
  }
}

?>


<?php include('footer.php'); ?>