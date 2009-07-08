<?php

/**
 * Player
 * 
 * @author Jonathan Borzilleri
 */
class Player extends BasePlayer
{
  
  /**
   *
   * @return bool
   */
  public function updateFromForm() {
    $error = false;
    
    // Race
    $this->race_id = (int)@$_POST['character_race'];
    // Archetype (Class)
    $this->archetype_id = (int)@$_POST['character_class'];

    // Character Name    
    $this->name = empty($_POST['character_name'])?
      'unknown':$_POST['character_name'];
    
    // Character Level
    if( empty($_POST['character_level']) ) {
      // level may not be blank.
      $error = true;
    }
    elseif( 1 > $_POST['character_level'] || 30 < $_POST['character_level'] ) {
      // Level must be 1-30 inclusive.
      $error = true;
    }
    else {
      $this->level = (int)$_POST['character_level'];
    }
    
    // Ability Scores
    
    // Strength
    if( empty($_POST['character_str']) ) {
      // Error, STR May not be empty.
      $error = true;
    }
    elseif( $_POST['character_str'] < 1 ) {
      // Error, STR must be positive.
      $error = true;
    }
    else {
      $this->strength = (int)$_POST['character_str'];
    }
    
    // Dexterity
    if( empty($_POST['character_dex']) ) {
      // Error, May not be empty.
      $error = true;
    }
    elseif( $_POST['character_dex'] < 1 ) {
      // Error, must be positive.
      $error = true;
    }
    else {
      $this->dexterity = (int)$_POST['character_dex'];
    }
    
    // Constitution
    if( empty($_POST['character_con']) ) {
      // Error, May not be empty.
      $error = true;
    }
    elseif( $_POST['character_con'] < 1 ) {
      // Error, must be positive.
      $error = true;
    }
    else {
      $this->constitution = (int)$_POST['character_con'];
    }
    
    // Intelligence
    if( empty($_POST['character_int']) ) {
      // Error, May not be empty.
      $error = true;
    }
    elseif( $_POST['character_int'] < 1 ) {
      // Error, must be positive.
      $error = true;
    }
    else {
      $this->intelligence = (int)$_POST['character_int'];
    }
    
    // Wisdom
    if( empty($_POST['character_wis']) ) {
      // Error, May not be empty.
      $error = true;
    }
    elseif( $_POST['character_wis'] < 1 ) {
      // Error, must be positive.
      $error = true;
    }
    else {
      $this->wisdom = (int)$_POST['character_wis'];
    }
    
    // Charisma
    if( empty($_POST['character_cha']) ) {
      // Error, May not be empty.
      $error = true;
    }
    elseif( $_POST['character_cha'] < 1 ) {
      // Error, must be positive.
      $error = true;
    }
    else {
      $this->charisma = (int)$_POST['character_cha'];
    }
    
    // Health Maximum
    // Calculate how much health this character 'should' have, based on their
    // class, level, and constitution score.
    $health_calc = $this->Archetype->health_first + $this->constitution +
      ($this->Archetype->health_level * ($this->level-1));
    if( empty($_POST['character_health']) || 1 > $_POST['character_health'] ||
        $health_calc > $_POST['character_health']) {
      // The entered value is empty, invalid, or less than our calculated value.
      // At this point, we check to see if the current value is empty,
      // or less than our calculated value. If so, we update the field using
      // our calculated value.
      if( empty($this->health_max) || $this->health_max < $health_calc ) {
        $this->health_max = $health_calc;
      }
    }
    else {
      $this->health_max = (int)$_POST['character_health'];
    }
    
    // Healing Surges Per Day
    // Calculate how many surges we have based on our current class/con modifier
    $surges_calc = $this->Archetype->surges + $this->getMod('con');
    if( empty($_POST['character_surges']) || 1 > $_POST['character_surges'] ||
        $surges_calc > $_POST['character_surges']) {
      // The entered value is empty, invalid, or less than our calculated value.
      
      // If our current surges/day is LESS than the calculated value, or empty,
      // we update the value, otherwise we leave it alone.
      if( $this->surges_max < $surges_calc ) {
        $this->surges_max = $surges_calc;
      }
    }
    else {
      $this->surges_max = (int)$_POST['character_surges'];
    }
    
    
    // Surge Value
    // If the current surge value is empty,
    // or if it is less than 1/4 of the newly updated maximum health,
    // then update it to be 1/4 of our new maximum health.
    /**
     * @todo If we do this, send a notice to alert the player to check their
     * surge value?
     */
    if( empty($this->surge_value) ||
        $this->surge_value < floor($this->health_max/4) ) {
      $this->surge_value = floor($this->health_max/4);
    }
    
    return !$error;
  }
  
  /**
   * Determines if the character is currently bloodied
   * (at or under 1/2 of their maximum health )
   * 
   * @return bool
   */
  public function isBloodied() {
    return $this->health_cur <= floor($this->health_max/2);
  }
  
  /**
   * Returns the modifier for a given Ability Score
   * An ability score modifier is equal to (score/2)-5
   *
   * Valid parameters are:
   * 'strength', 'str'
   * 'dexterity', 'dex'
   * 'constitution', 'con'
   * 'intelligence, 'int'
   * 'wisdom', 'wis'
   * 'charisma', 'cha'
   *
   * @param string $ability The name of the ability score, eg 'strength'
   * @return int
   */
  public function getMod($ability) {      
    return floor($this->getAbilityScore($ability)/2)-5;
  }
  
  private function getAbilityScore($ability) {
    switch(strtolower($ability)) {
      case 'strength':
      case 'str':
        return $this->strength;
        break;
      case 'dexterity':
      case 'dex':
        return $this->dexterity;
        break;
      case 'constitution':
      case 'con':
        return $this->constitution;
        break;
      case 'intelligence':
      case 'int':
        return $this->intelligence;
        break;
      case 'wisdom':
      case 'wis':
        return $this->wisdom;
        break;
      case 'charisma':
      case 'cha':
        return $this->charisma;
        break;
      default:
        return 0;
        break;
    }
  }
}

?>
