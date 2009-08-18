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
    
    // Pull the last character seen, so we can reference the id afterwards:
    $last_char = Doctrine_Query::create()->select('p.id')->from('Player p')
      ->orderBy('p.id DESC')->limit(1)->execute();
    $last_char = $last_char->count()>0 ? $last_char->getFirst() : null;
   
		// Pull the player name out of the file.
		$c = file_get_contents(IMPORT_PATH.$_POST['file_name']);
		preg_match('/Player:\s+\w+:\s+name: "?(.*?)"?\s/', $c, $m);
		$char_name = $m[1];

    /**
     * The Doctrine::loadData method does not (as far as I can determine)
     * have any useful return value. Stuff this in a try/catch block, so 
     * if something goes horribly wrong, we can at least attempt to recover
     * gracefully.
     */
    try {
			// First, attempt to delete the old character
			$p_list = Doctrine::getTable('Player')->findByName($char_name);
			if( $p_list && $p_list->count > 0 ) {
				foreach($p_list as $p) {
					$p->delete();
				}
			}
      Doctrine::loadData(IMPORT_PATH.$_POST['file_name'], true);
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
     */
		$this_char = Doctrine_Query::create()
			->from('Player p')
      ->orderBy('p.id DESC')->limit(1)->execute();
    $this_char = $this_char->count()>0 ? $this_char->getFirst() : null;
    
    
    if( $this_char && ( !$last_char || $this_char->id > $last_char->id ) ) {
      /**
       * I don't know WHY, but for some reason we're not getting the
       * Archetype relation on the Player query above.
       *
       * So we manually pull down the Archetype into $a, below, and 
       * assign it to $this_char->Archetype.
       *
       * This makes sure the call to initalizeCurrentValues() works properly.
       */
      $a = Doctrine_Query::create()->select('a.id')->from('Archetype a')
        ->where('a.id = ?', $this_char->archetype_id)->execute()->getFirst();
      $this_char->Archetype = $a;
      
      /**
       * We need to initalize our current values, because for a fresh player
       * they're based on derived values, which we dont have pre-insert
       */
      $this_char->initializeCurrentValues();
      $this_char->save();
    }
    else {
      $msg->add('An unknown error occured, please try again.',
        Message::ERROR);
      loadPage($target_uri);
    }
    
    $target_uri = "/{$this_char->id}";
    break;
}

loadPage($target_uri);
include('footer.php');
?>
