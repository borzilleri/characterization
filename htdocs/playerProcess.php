<?php
include('autohandler.php');

$char = Doctrine::getTable('Player')->findOneById(@$_POST['id']);
if( !$char || !$char->exists() ) {
  loadPage("/");
}
$success = true;

switch($action) {
  case 'shortRest':
    $success = $char->shortRest();
    break;
  case 'extendedRest':
    $success = $char->extendedRest();
    break;
  case 'surge':
    $success = $char->useSurge((int)@$_POST['surge_bonus']);
    break;
  case 'damage':
    $success = $char->takeDamage((int)@$_POST['health']);
    break;
  case 'tempHealth':
    $success = $char->addTempHealth((int)@$_POST['health']);
    break;
  case 'notes':
    $char->notes = trim(@$_POST['notes']);
    break;
}

if( $success ) { 
  $char->save();
}

loadPage("/{$_POST['id']}");
include('footer.php');
?>
