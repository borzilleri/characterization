<?php

/**
 * Archetype
 * 
 * 
 * @author Jonathan Borzilleri
 */
class Archetype extends BaseArchetype
{
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
        ->select('a.id')
        ->from('Archetype a');
      $classes = $q->execute();
      
      foreach($classes as $c) {
        $field .= '<option value="'.$c->id.'">'.$c->name.'</option>';
      }
      
      $field .= "</select>";
      return $field;
  }

}
?>
