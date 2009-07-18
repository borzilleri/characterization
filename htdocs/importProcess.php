<?php
include('autohandler.php');

switch($action) {
  case 'upload':
    if( empty($_POST['file_name']) || 
        '.' == substr($_POST['file_name'],0,1) ||
        ( '.yml' != substr($_POST['file_name'],-4) &&
          '.yaml' != substr($_POST['file_name'],-5) ) ) {
      $msg->add('Invalid filename provided.', Message::ERROR);
    }
    elseif( !is_readable(IMPORT_PATH.$_POST['file_name']) ) {
      $msg->add('File does not exist or is unreadable.', Message::ERROR);
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
      loadPage('/import');
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
    
    loadPage("/{$last_char[0]->id}");    
    break;
}

loadPage('/import');
include('footer.php');
?>