<?php

/**
 * Race
 * 
 * @author     Jonathan Borzilleri
 */
class Race extends BaseRace {
  
  /**
   *
   * @param string $name The 'name' attribute for the select element.
   * @param string $id The 'id' attribute for the select element.
   * @param string $selected The value of the currently selected item.
   * @return string
   */
  public function generateSelect($name, $id = null, $selected = null) {
      $field = '<select name="'.$name.'"';
      $field .= !empty($id)?'id="'.$id.'"':'';
      $field .= ">";
      
      $q = Doctrine_Query::create()
        ->select('r.id')
        ->from('Race r');
      $races = $q->execute();
      
      foreach($races as $r) {
        $field .= '<option value="'.$r->id.'"';
        if( $selected == $r->id ) {
          $field .= ' selected="selected"';
        }
        $field .= '>'.$r->name.'</option>';
      }
      
      $field .= "</select>";
      
      return $field;
  }
}
?>
