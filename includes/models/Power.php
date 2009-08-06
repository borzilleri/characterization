<?php

/**
 * Power
 * 
 * @author Jonathan Borzilleri
 */
class Power extends BasePower
{
	private $tmp_keywords = array();
	const POWER_ATWILL = '1_atwill';
	const POWER_ENCOUNTER = '2_encounter';
	const POWER_DAILY = '3_daily';

  /**
   * Pre-Delete handling, we must delete our keyword associations first.
   */
	public function preDelete() {
		$this->PowerKeywords->delete();
	}
	
	/**
	 * Post-save handling, update our keywords appropriately.
	 */
	public function postSave() {
		// After saving the Power, we need to go through the $tmp_keywords array
		// and update any objects there with our ID, and save them.
		foreach($this->tmp_keywords as $k) {
			$k->power_id = $this->id;
			$k->save();
		}
	}

  /**
   *
   * @global Message
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

		// Attack Bonus
		if( !empty($_POST['attack_bonus']) && !is_numeric($_POST['attack_bonus'])) {
			$msg->add('Attack Bonus must be numeric', Message::WARNING);
			$error = true;
		}
		else {
			$this->attack_bonus = (int)trim(@$_POST['attack_bonus']);
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
    $this->power_range = trim(@$_POST['power_range']);
    $this->hit = trim(@$_POST['hit']);
    $this->miss = trim(@$_POST['miss']);
    $this->effect = trim(@$_POST['effect']);
    $this->notes = trim(@$_POST['notes']);
		
		// Power Keywords
		if( !empty($_POST['keywords']) && is_array($_POST['keywords']) ) {
			// First: Iterate through the _POST keyword array
			// For any keyword we don't already have, make a new PowerKeyword object
			// and add it to the tmp_keywords array
			foreach($_POST['keywords'] as $k_id) {
				if( !$this->Keywords->contains($k_id) ) {
					$k = new PowerKeyword;
					$k->keyword_id = $k_id;
					$this->tmp_keywords[] = $k;
				}
			}
			
			// Second: Iterate through our existing keywords,
			// Any that DO NOT exist in the _POST array should be unlinked
			$keywords_to_unlink = array();
			foreach($this->Keywords as $k) {
				if( !in_array($k->id, $_POST['keywords']) ) {
					$keywords_to_unlink[] = $k->id;
				}
			}
			if( !empty($keywords_to_unlink) ) {
				$this->unlink('Keywords', $keywords_to_unlink);
			}
		}		

    return !$error;    
  }
  
  /**
   * Determines if an action type is valid.
   *
   * @param string $action The action type to validate.
   * @return bool
   **/
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

  /**
   * Determines if an attack ability score is valid
   *
   * @param string $stat the Ability score name to validate
   * @return bool
   */
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
  /**
   * Determines if a defense is valid
   *
   * @param string $def the Defense to validate
   * @return bool
   */
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
  /**
   * Determines if the sustaion action type is valid
   *
   * @param string $action The sustain action to validate.
   * @return bool
   */
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
  
  /**
   * Determines if the usage is valid
   *
   * @param string $type The usage type to validate
   * @return bool
   */
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
  
  /**
   * Determines if this power is of the usage type passed in
   *
   * @param string $type A POWER_* class constant.
   * @return bool
   */
  public function isUseType($type) {
    return $type == $this->use_type;
  }

  /**
   * Return a friendly display string for the power's usage type
   *
   * @return string
   */
	public function getUseTypeDisplay() {
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
	
	/**
	 * Refresh the power, allowing it to be used again.
	 */ 
  public function refresh() {
    $this->used = false;
  }
  
  /**
   * Use a power, expending it.
   *
   * At-Will Powers cannot be expended.
   *
   * @return bool
   */
  private function usePower() {
    if( self::POWER_ATWILL != $this->use_type ) {
      $this->used = true;
      return true;
    }
    else {
      return false;
    }
  }
  
  /**
   * Toggle a power's expended state.
   *
   * At-Will Powers cannot be toggled.
   *
   * @return bool
   */
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
  
  /**
   * Return a friendly display string for an action type.
   *
   * @return string
   */
  public function getActionTypeDisplay() {
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
  
  /**
   * Perform post-processing on text fields
   *
   * @param string $field The field to process.
   * @param bool $echo Whether to echo the result.
   * @return string
   */
  public function getTextFieldDisplay($field, $echo = false) {
    $out = $this->$field;
    
    $out = htmlentities($out);
    $out = nl2br($out);
    
    switch($field) {
      case 'hit':
        // Secondary Attack
        $out = preg_replace('/(secondary attack:)/i','<label>$1</label>',$out);
        // Secondary Taraget
        $out = preg_replace('/(secondary target:)/i','<label>$1</label>',$out);
        // Hit
        $out = preg_replace('/(hit:)/i', '<label>$1</label>', $out);
        // Aftereffect
        $out = preg_replace('/(aftereffect:)/i', '<label>$1</label>', $out);
        break;
      case 'notes':
        // Special
        $out = preg_replace('/(special:)/i', '<label>$1</label>', $out);
        break;
      default:
        break;
    }
    
    if( $echo ) echo $out;
    return $out;
  }

  /**
   * Check to see if a power has a given keywords 
   *
   * @param string $keyword A Keyword string
   * @return bool
   */
  public function hasKeyword($keyword) {
    $k = Doctrine::getTable('Keyword')->findOneByName($keyword);
    return ($k && $k->exists() && $this->Keywords->contains($k->id));
  }
  
  /**
   * Return an array of attack bonuses for this power. 
   *
   * @uses Player::getMod()
   * @uses Player::getAttackBonus()
   * @return array
   */
  public function getAttackBonusTable() {
    $power_bonus = $this->attack_bonus;
    $power_bonus += (int)$this->Player->getMod($this->attack_ability);
    
    if( $this->hasKeyword('Implement') ) {
      return $this->Player->getAttackBonus(
        Player::ATTACK_IMPLEMENT, $power_bonus);
    }
    elseif( $this->hasKeyword('Weapon') ) {
      return $this->Player->getAttackBonus(
        Player::ATTACK_WEAPON, $power_bonus);
    }
  }
  
  /**
   * Generate a friendly attack bonus display
   *
   * @uses getAttackBonusTable()
   * @return string
   */
  public function getAttackBonusDisplay() {
    $bonus_table = $this->getAttackBonusTable();
    foreach($bonus_table as $k => $b) {
      if( $b >= 0 ) {
        $bonus_table[$k] = '+'.$bonus_table[$k];
      }
    }
    return implode('/',$bonus_table);
  }

	/**
	 * Generates the html for a power box and returns it.
	 *
	 * @uses getUseTypeDisplay()
	 * @uses getTextFieldDisplay()
	 * @uses getActionTypeDisplay()
	 * @uses getAttackBonusDisplay()
	 * @uses Archetype::$name
	 * @param bool $collapseUsed Whether to collapse the power box to it's title
	 *  bar based on the used property.
	 * @param bool $echo Whether to print the generated box or return it.
	 * @return string
	 */
	public function getPowerBoxDisplay($collapseUsed = false, $echo = false) {
		$box = ""; $i = 0;
		
		// Outer div
		$box .= '<div class="power" id="powerBox'.$this->id.'">';
		
		// TitleBar
		$box .= '<div id="p'.$this->id.'" class="titleBar '.
			$this->getUseTypeDisplay().'">';
		// Power Class/Level
		$box .= '<span class="powerLevel">'.$this->Player->Archetype->name.
			' '.$this->level.'</span>';
		// Power Title
		$box .= '<span class="powerTitle">'.$this->name.'</span>';
		$box .= '</div><!-- end div.titleBar -->';
		// End TitleBar
		
		// Description
		// Only add the 'usedPower' class if the power is used AND we're
		// collapsing powers.
		$box .='<div class="description '.
		  ($collapseUsed&&$this->used?'usedPower':'').'">';
		
		
		// Statblock Row
		$box .= '<div class="row'.($i%2).'" id="p'.$this->id.'usage">'; $i+=1;
		
		// Usage/Keywords
		$box .= '<div>';
		// Usage
		$box .= '<span class="usage">'.$this->getUseTypeDisplay().'</span>';		
		// Keywords
		if( $this->Keywords->count() ) {
			$box .= ' &diams; <span class="keywords">';
			$first = true;
			foreach($this->Keywords as $k) {
				$box .= (!$first?', ':'').$k->name;
				$first = false;
			}
			$box .= '</span>';
		}
		$box .= '</div>';
		// End Usage/Keywords
		
		// Action/Range
		$box .= '<div>';
		// Range
		if( !empty($this->power_range) ) {
			$box .= '<span class="range">'.
			  $this->getTextFieldDisplay('power_range').'</span>';
		}
		// Action Type
		$box .= '<span class="actionType">'.$this->getActionTypeDisplay().'</span>';
		$box .= '</div>';
		// End Action/Range
		
		// Target
		if( !empty($this->target) ) {
			$box .= '<div><label>Target: </label><span>'.
			  $this->getTextFieldDisplay('target').'</span></div>';
		}
		
		// Attack
		if( 'none' != $this->attack_ability ) {
			$box .= '<div><label>Attack: </label><span>';
			$box .= $this->getAttackBonusDisplay().' ('.$this->attack_ability.')';
			$box .= ' vs. '.$this->defense.'</span></div>';
		}
		
		// End Statblock Row
		$box .= '</div>';
		
		// Hit
		if( !empty($this->hit) ) {
			$box .= '<div class="row'.($i%2).'" id="p'.$this->id.'hit">'; $i+=1;
			$box .= '<label>Hit: </label>';
			$box .= '<span>'.$this->getTextFieldDisplay('hit').'</span></div>';
		}

		// Miss
		if( !empty($this->miss) ) {
			$box .= '<div class="row'.($i%2).'" id="p'.$this->id.'miss">'; $i+=1;
			$box .= '<label>Miss: </label>';
			$box .= '<span>'.$this->getTextFieldDisplay('miss').'</span></div>';
		}
		
		// Effect
		if( !empty($this->effect) ) {
			$box .= '<div class="row'.($i%2).'" id="p'.$this->id.'effect">'; $i+=1;
			$box .= '<label>Effect: </label>';
			$box .= '<span>'.$this->getTextFieldDisplay('effect').'</span></div>';
		}

		// Sustain
		if( 'none'!=$this->sustain_action ) {
			$box .= '<div class="row'.($i%2).'" id="p'.$this->id.'sustain">'; $i+=1;
			$box .= '<label>Sustain '.$this->sustain_action.': </label>';
			$box .= '<span>'.$this->getTextFieldDisplay('sustain').'</span></div>';
		}
		
		// Notes
		if( !empty($this->notes) ) {
			$box .= '<div class="row'.($i%2).'" id="p'.$this->id.'notes">'; $i+=1;
			$box .= '<label>Notes: </label>';
			$box .= '<span>'.$this->getTextFieldDisplay('notes').'</span></div>';
		}
		
		// End Description
		$box .= '</div><!-- end div.description -->';
		// End Outer Div
		$box .= '</div><!-- end div#powerBox'.$this->id.' -->';
		$box .= "\n\n";
				
		if( $echo ) echo $box;
		return $box;		
	}
}
?>
