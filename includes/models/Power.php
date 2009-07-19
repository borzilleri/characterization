<?php

/**
 * Power
 * 
 * @author Jonathan Borzilleri
 */
class Power extends BasePower
{
	const POWER_ATWILL = '1_atwill';
	const POWER_ENCOUNTER = '2_encounter';
	const POWER_DAILY = '3_daily';
  public static $use_type_strings = array(
    '1_atwill' => 'atwill',
    '2_encounter' => 'encounter',
    '3_daily' => 'daily'
  );
  public static $use_type_strings_rev = array(
    'atwill' => '1_atwill',
    'encounter' => '2_encounter',
    'daily' => '3_daily'
  );

	public function preDelete() {
		$this->PowerKeywords->delete();
	}

  /**
   *
   * @global $msg
   * @return bool
   */
  public function updateFromForm() {
    global $msg;
    $error = false;
    
    // Power Name
    if( empty($_POST['power_name']) ) {
      $msg->add('Name must not be empty.', Message::WARNING);
      $error = true;
    }
    else {
      $this->name = trim($_POST['power_name']);
    }
    
    // Level
    if( empty($_POST['level']) ||
        1 > $_POST['level'] || 30 < $_POST['level'] ) {
      $msg->add('Level must be between 1 and 30.', Message::WARNING);
      $error = true;
    }
    else {
      $this->level = (int)$_POST['level'];
    }
    
    // UseType
    if( empty($_POST['use_type']) || 
        !$this->isValidUseType($_POST['use_type']) ) {
      $msg->add('Usage Type must be "at-will", "encounter", or "daily."',
        Message::WARNING);
      $error = true;
    }
    else {
      $this->use_type = $_POST['use_type'];
    }
    
    // Action Type
    if( empty($_POST['action_type']) || 
        !$this->isValidActionType($_POST['action_type']) ) {
      $msg->add('Invalid action type.', Message::WARNING);
      $error = true;
    }
    else {
      $this->action = $_POST['action_type'];
    }
    
    // Attack Stat
    if( empty($_POST['attack_ability']) || 
        !$this->isValidAttackStat($_POST['attack_ability']) ) {
      $msg->add('Invalid Attack Stat', Message::WARNING);
      $error = true;
    }
    else {
      $this->attack_ability = $_POST['attack_ability'];
    }
    
    // Attack Defense
    if( empty($_POST['defense']) ||
        !$this->isValidDefense($_POST['defense']) ) {
      $msg->add('Invalid Defense', Message::WARNING);
      $error = true;  
    }
    else {
      $this->defense = $_POST['defense'];
    }
    
    // Sustain Action
    if( empty($_POST['sustain_action']) ||
        !$this->isValidSustainAction($_POST['sustain_action']) ) {
      $msg->add('Invalid Sustain Action', Message::WARNING);
      $error = true;
    }
    else {
      $this->sustain_action = $_POST['sustain_action'];
      $this->sustain = trim(@$_POST['sustain']);
    }
    
    $this->target = trim(@$_POST['target']);
    $this->attack_bonus = trim(@$_POST['attack_bonus']);
    $this->power_range = trim(@$_POST['power_range']);
    $this->hit = trim(@$_POST['hit']);
    $this->miss = trim(@$_POST['miss']);
    $this->effect = trim(@$_POST['effect']);
    $this->notes = trim(@$_POST['notes']);
    
    return !$error;    
  }
  
  private function isValidActionType($action) {
    switch($this->action) {
      case 'standard':
      case 'move':
      case 'minor':
      case 'free':
      case 'interrupt':
      case 'reaction':
      case 'none':
        return true;
        break;
      default:
        return false;
        break;
    }
  }
  private function isValidAttackStat($stat) {
    switch($stat) {
      case 'Str':
      case 'Con':
      case 'Dex':
      case 'Int':
      case 'Wis':
      case 'Cha':
      case 'none':
        return true;
        break;
      default:
        return true;
        break;
    }
  }
  private function isValidDefense($def) {
    switch($def) {
      case 'AC':
      case 'Fort':
      case 'Ref':
      case 'Will':
        return true;
        break;
      default:
        return false;
        break;
    }
  }
  private function isValidSustainAction($action) {
    switch($action) {
      case 'standard':
      case 'move':
      case 'minor':
      case 'free':
      case 'none':
        return true;
        break;
      default:
        return false;
        break;
    }
  }
  
  public function isValidUseType($type) {
		switch($type) {
			case self::POWER_ATWILL:
			case self::POWER_ENCOUNTER:
			case self::POWER_DAILY:
				return true;
				break;
			default:
				return false;
				break;
		}
  }
  public function isUseType($type) {
    return $type == $this->use_type;
  }

	public function getDisplayUseType() {
		switch($this->use_type) {
			case self::POWER_ENCOUNTER:
				return 'Encounter';
				break;
			case self::POWER_DAILY:
				return 'Daily';
				break;
			case self::POWER_ATWILL:
			default:
				return 'At-Will';
				break;
		}
	}
	  
  public function refresh() {
    $this->used = false;
  }
  
  private function usePower() {
    if( self::POWER_ATWILL != $this->use_type ) {
      $this->used = true;
      return true;
    }
    else {
      return false;
    }
  }
  
  public function togglePower() {
    if( self::POWER_ATWILL == $this->use_type ) {
      $this->used = false;
      return false;
    }
    else {
      $this->used = !$this->used;
      return true;
    }
  }
  
  public function actionTypeDisplay() {
    switch($this->action) {
      case 'move':
        return 'Move Action';
        break;
      case 'minor':
        return 'Minor Action';
        break;
      case 'free':
        return 'Free Action';
        break;
      case 'interrupt':
        return 'Immediate Interrupt';
        break;
      case 'reaction':
        return 'Immediate Reaction';
        break;
      case 'none':
        return 'No Action';
        break;
      case 'standard':
      default:
        return "Standard Action";
        break;
    }
  }
}
?>
