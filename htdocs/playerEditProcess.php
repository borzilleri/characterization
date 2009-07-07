<?php
include('autohandler.php');

if( 'save' == $action ) {
  if( !empty($_POST['id']) ) {
      $char = new Player;
      $redir_url = "/new";
  }
  else {
      $char = Doctrine::getTable('Player')->findOneByID($_POST['id']);
      $redir_url = "/{$_POST['id']"
  }
  
  $char->updateFromForm();
  
  
}


include('footer.php');
?>
