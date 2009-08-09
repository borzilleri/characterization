<?php
include('autohandler.php');

$target_uri = '/import';
switch($action) {
  case 'upload':
    if( empty($_POST['file_name']) || 
        '.' == substr($_POST['file_name'],0,1) ||
        ( '.yml' != substr($_POST['file_name'],-4) &&
          '.yaml' != substr($_POST['file_name'],-5) ) ) {
      $msg->add('Invalid filename provided.', Message::ERROR);
      break; // Break out of the switch and just redirect
    }
    elseif( !is_readable(IMPORT_PATH.$_POST['file_name']) ) {
      $msg->add('File does not exist or is unreadable.', Message::ERROR);
      break; // Break out of the switch and just redirect
    }
    
    /**
     * The Doctrine::loadData method does not (as far as I can determine)
     * have any useful return value. Stuff this in a try/catch block, so 
     * if something goes horribly wrong, we can at least attempt to recover
     * gracefully.
     */
    try {
      Doctrine::loadData(IMPORT_PATH.$_POST['file_name']);
    }
    catch( Exception $ex ) {
      $msg->add('An exception occured while loading the file '
        .$_POST['file_name'].'. Message: '.$ex->getMessage(), Message::ERROR);
      /**
       * @todo figure out how to remove this, and shunt out of the switch.
       * Maybe just use a break statement?
       */
      loadPage($target_uri);
    }
    
    /**
     * By now, we /assume/ the data's been loaded successfully.
     * We still need to know what the ID of the inserted player record is, so
     * we'll have to pull that out of the DB
     *
     * @todo We can probably pull the highest ID before/after and compare them
     * to see if the import was ACTUALLY successfull.
     */
    $last_char = Doctrine_Query::create()
      ->select('p.id')->from('Player p')
      ->orderBy('p.id DESC')->limit(1)->execute();
    
    $target_uri = "/{$last_char[0]->id}";
    break;
}

loadPage($target_uri);
include('footer.php');
?>