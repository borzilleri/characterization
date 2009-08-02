<?php

/**
 * Player
 * 
 * @author Jonathan Borzilleri
 */
class Player extends BasePlayer
{
  const STATUS_DEAD = 'Dead';
  const STATUS_UNCONSCIOUS = 'Unconscious';
  const STATUS_BLOODIED = 'Bloodied';
  
  const ATTACK_WEAPON = 'Weapon';
  const ATTACK_IMPLEMENT = 'Implement';

  public function preDelete() {
		$this->Powers->delete();
	}

  /**
   * @global $msg
   * @return bool
   */
  public function updateFromForm() {
    global $msg;
    $error = false;
    
    // Race
    $this->race_id = (int)@$_POST['character_race'];
    // Archetype (Class)
    $this->archetype_id = (int)@$_POST['character_class'];

    // Character Name    
    $this->name = empty($_POST['character_name'])?
      'unknown':$_POST['character_name'];
    
    // Character Level
    if( empty($_POST['character_level']) ||
        1 > $_POST['character_level'] || 30 < $_POST['character_level'] ) {
      $msg->add('Level must be 1-30 inclusive.', Message::WARNING);
      $error = true;
    }
    else {
      $this->level = (int)$_POST['character_level'];
    }
    
    // Ability Scores
    
    // Strength
    if( empty($_POST['character_str']) || $_POST['character_str'] < 1 ) {
      $msg->add('Strenth must be a positive number.', Message::WARNING);
      $error = true;
    }
    else {
      $this->strength = (int)$_POST['character_str'];
    }
    
    // Dexterity
    if( empty($_POST['character_dex']) || $_POST['character_dex'] < 1 ) {
      $msg->add('Dexterity must be a positive number.', Message::WARNING);
      $error = true;
    }
    else {
      $this->dexterity = (int)$_POST['character_dex'];
    }
    
    // Constitution
    if( empty($_POST['character_con']) || $_POST['character_con'] < 1 ) {
      $msg->add('Constitution must be a positive number.', Message::WARNING);
      $error = true;
    }
    else {
      $this->constitution = (int)$_POST['character_con'];
    }
    
    // Intelligence
    if( empty($_POST['character_int']) || $_POST['character_int'] < 1 ) {
      $msg->add('Intelligence must be a positive number.', Message::WARNING);
      $error = true;
    }
    else {
      $this->intelligence = (int)$_POST['character_int'];
    }
    
    // Wisdom
    if( empty($_POST['character_wis']) || $_POST['character_wis'] < 1 ) {
      $msg->add('Wisdom must be a positive number.', Message::WARNING);
      $error = true;
    }
    else {
      $this->wisdom = (int)$_POST['character_wis'];
    }
    
    // Charisma
    if( empty($_POST['character_cha']) || $_POST['character_cha'] < 1 ) {
      $msg->add('Charisma must be a positive number.', Message::WARNING);
      $error = true;
    }
    else {
      $this->charisma = (int)$_POST['character_cha'];
    }
    
    // General Attack Bonus
    if( !is_numeric($_POST['attack_general']) || 
        (int)$_POST['attack_general'] < 0 ) {
      $msg->add('General Attack Bonus must be a non-negative integer.', 
        Message::WARNING);
      $error = true;
    }
    else {
      $this->attack_general = (int)$_POST['attack_general'];
    }

    // Implement Attack Bonus
    if( !is_numeric($_POST['attack_implement']) ||
        (int)$_POST['attack_implement'] < 0 ) {
      $msg->add('Implement Attack Bonus must be a non-negative integer.', 
        Message::WARNING);
      $error = true;
    }
    else {
      $this->attack_implement = (int)$_POST['attack_implement'];
    }

    // Main Hand Weapon Bonus
    if( !is_numeric($_POST['attack_weapon_main']) ||
        (int)$_POST['attack_weapon_main'] < 0 ) {
      $msg->add('Main Hand Weapon Attack Bonus must be a non-negative integer.', 
        Message::WARNING);
      $error = true;
    }
    else {
      $this->attack_weapon_main = (int)$_POST['attack_weapon_main'];
    }

    // Off Hand Weapon Attack Bonus
    if( !is_numeric($_POST['attack_weapon_off']) ||
        (int)$_POST['attack_weapon_off'] < 0 ) {
      $msg->add('Off Hand Weapon Attack Bonus must be a non-negative integer.', 
        Message::WARNING);
      $error = true;
    }
    else {
      $this->attack_weapon_off = (int)$_POST['attack_weapon_off'];
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
    $surge_v_calc = floor($this->health_max/4);
    if( empty($_POST['character_surge_value']) || 
        1 > $_POST['character_surge_value'] || 
        $surge_v_calc > $_POST['character_surge_value'] ) {
      $this->surge_value = $surge_v_calc;   
    }
    else {
      $this->surge_value = $_POST['character_surge_value'];
    }

    // Current Health
    if( !$this->exists() ) {
      $this->health_cur = $this->health_max;
    }

    // Current Surges
    if( !$this->exists() ) {
      $this->surges_cur = $this->surges_max;
    }
    
    return !$error;
  }
  
  public function getBloodiedValue() {
    return floor($this->health_max/2);
  }
  /**
   * Determines if the character is currently bloodied
   * (at or under 1/2 of their maximum health )
   * 
   * @return bool
   */
  public function isBloodied() {
    return $this->health_cur <= $this->getBloodiedValue();
  }
  
  /**
   * Determines if the chracter is currently unconscious
   *
   */
  public function isUnconscious() {
    return ($this->health_cur < 1 
      && $this->health_cur > -1*$this->getBloodiedValue() );
  }
  
  /**
   * Determines if the character is currently dead
   *
   * Death is determined by:
   * 1) health_cur is equal to -1*floor(health_max/2)
   *    ie - the character's bloodied value as a negative number.
   * 2) 3 failed death saving throws
   *
   * @return bool 
   */
  public function isDead() {
    if( $this->health_cur <= -1*$this->getBloodiedValue() ) {
      return true;
    }
    /**
     * @todo Add checks for death saving throws
     */
    
    return false;
  }
  
  public function getStatusText() {
    if( $this->isDead() ) {
      return self::STATUS_DEAD;
    }
    elseif( $this->isUnconscious() ) {
      return self::STATUS_UNCONSCIOUS;
    }
    elseif( $this->isBloodied() ) {
      return self::STATUS_BLOODIED;
    }
    
    return '';
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
    $ability = $this->getAbilityScore($ability);
    if( 0 <= $ability ) {
      return floor($ability/2)-5;
    }
    else {
      return 0;
    }
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
        return -1;
        break;
    }
  }
  
  public function doRest($restType = 'short') {
    if( 'extended' == $restType ) {
      $this->extendedRest();
    }
    else {
      $this->shortRest();
    }
    return true;
  }
  
  /**
   * Perform a short rest action.
   *
   * A short rest action refreshes (makes active/unused) all encounter powers.
   * 
   * @return bool
   */
  public function shortRest() {
    $this->health_tmp = 0;

    foreach( $this->Powers as $p ) {
      if( Power::POWER_ENCOUNTER == $p->use_type ) {
        $p->refresh();
      }
    }
    return true;
  }
    
  /**
   * Perform an extended rest action.
   * 
   * An extended rest returns health to maximum, surges per day to maximum, 
   * as well as refreshes (makes active/unused) all encounter and daily powers.
   *
   * @return bool
   */
  public function extendedRest() {
    $this->health_cur = $this->health_max;
    $this->surges_cur = $this->surges_max;
    $this->health_tmp = 0;
    
    // NOTE: This is implemented using custom-house rules for action points
    /**
     * @todo Add a per-player option for default/house ruled action points
     */
    $this->action_points = 0;
    
    // Refresh Encounter & Daily Powers
    foreach( $this->Powers as $p ) {
      $p->refresh();
    }
    
    return true;
  }

  public function addSurge() {
    $this->surges_cur = min($this->surges_cur+1,$this->surges_max);
    return true;
  }
  
  public function subtractSurge() {
    global $msg;
    $error = false;
    
    if( 1 > $this->surges_cur ) {
      $msg->add('You do not have any surges left.', Message::NOTICE);
      $error = true;
    }
    else {
      $this->surges_cur = max($this->surges_cur-1,0);
    }
    return !$error;
  }
  
  /**
   * Attempt to use a healing surge.
   *
   * This method attempts to use a healing surge on the character. If the 
   * character has zero healing surges left, or is at max health, nothing will
   * happen and a notice will be sent. The exception is if the character is
   * under 1 health, at which point they are set to 1 health.
   * 
   * @global $msg
   * @param int $extra Extra health to be added along with the surge value.
   * @return bool
   */
  public function useSurge($extra = 0) {
    global $msg;
    $error = false;

    if( 1 > $this->surges_cur ) {
      if( $this->health_cur < 1 ) {
        $this->health_cur = 1;
        $msg->add("You have no healing surges remaining, health set to 1.",
          Message::NOTICE);
      }
      else {
        $msg->add("You do not have a healing surge to spend", Message::WARNING);
        $error = true;
      }
    }
    elseif( $this->health_cur >= $this->health_max ) {
      $msg->add("You are at maximum health.", Message::NOTICE);
      $error = true;
    }
    else {
      $this->surges_cur = $this->surges_cur - 1;
      $this->healDamage($this->surge_value+(int)$extra);
    }
    
    return !$error;
  }
  
  /**
   * Heals an amount of health.
   *
   * @param int health
   * @return bool
   */
  private function healDamage($health) {
    $error = false;

    if( 1 > $health ) {
      $msg->add('You cannot heal negative hit points', Message::ERROR);
      $error = true;
    }
    else {
      $this->health_cur = min(
        $this->health_max,($this->health_cur+(int)$health));
    }
    return !$error;
  }
  
  /**
   * Take an amount of damage.
   *
   * @global $msg
   * @return bool
   */
  public function takeDamage($damage) {
    global $msg;
    $error = false;
    $damage_left = (int)$damage;
    
    if( $this->isDead() ) {
      $msg->add("Unable to take damage. You are dead.", Message::WARNING);
      $error = true;
    }
    else {
      if( 0 > $damage ) {
        $this->healDamage($damage*-1);
      }
      elseif( $this->health_tmp >= $damage_left ) {
        // If we have more temporary hp than damage taken,
        // Just remove the temp hp.
        $this->health_tmp = $this->health_tmp - $damage_left;
      }
      else {
        // Otherwise, subtract our temp hp from the damage, set it to zero
        // And remove the rest of the damage from our current health.
        $damage_left = $damage_left - $this->health_tmp;
        $this->health_tmp = 0;
        
        $this->health_cur = $this->health_cur - $damage_left;
      }
    }
    
    return !$error;
  }
  
  /**
   * @global $msg
   * @param int $health Temporary HP to add.
   * @return bool
   */
  public function addTempHealth($health) {
    global $msg;
    $error = false;
    
    if( 0 > $health ) {
      $msg->add("May not add negative temporary health.", Message::WARNING);
      $error = true;
    }
    elseif( $this->health_tmp > $health ) {
      $msg->add("Unable to add temporary health. Larger source already exists.", 
        Message::NOTICE);
      $error = true;
    }
    else {
      $this->health_tmp = $health;
    }
    return true;
  }
  
  /**
   * Add an Action Point
   *
   * @return integer
   */
   public function addActionPoint() {
     $this->action_points = $this->action_points+1;
     return true;
   }
   
   /**
    * Subtract an Action Point
    * 
    * @return integer
    */
  public function subtractActionPoint() {
    $this->action_points = max(0, $this->action_points-1);
    return true;
  }
  
  /**
   * Returns the attack bonus using a given accessory.
   *
   *
   * @param string $accessory Accessory type to use for the attack.
   * @return integer
   */
  public function getAttackBonus($accessory = self::ATTACK_WEAPON) {
    $bonus = floor($this->level/2) + $this->attack_general;    
    switch($accessory) {
      case self::ATTACK_WEAPON:
        $bonus += $this->attack_weapon_main;
        break;
      case self::ATTACK_IMPLEMENT:
        $bonus += $this->attack_implement;
        break;
      default:
        break;
    }
    return $bonus;
  }
}

?>
