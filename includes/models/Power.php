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

	public function preDelete() {
		$this->PowerKeywords->delete();
	}
	
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

	/**
	 * Generates the html for a power box and returns it.
	 *
	 * @param string $echo Whether to print the generated box or return it.
	 * @return string
	 */
	public function getPowerBox($echo = false) {
		$box = ""; $i = 0;
		
		// Outer div
		$box .= '<div class="power" id="powerBox'.$this->id.'">';
		
		// TitleBar
		$box .= '<div id="p'.$this->id.'" class="titleBar '.
			$this->getDisplayUseType().'">';
		// Power Class/Level
		$box .= '<span class="powerLevel">'.$this->Player->Archetype->name.
			' '.$this->level.'</span>';
		// Power Title
		$box .= '<span class="powerTitle">'.$this->name.'</span>';
		$box .= '</div><!-- end div.titleBar -->';
		// End TitleBar
		
		// Description
		$box .='<div class="description '.($this->used?'usedPower':'').'">';
		
		
		// Statblock Row
		$box .= '<div class="row'.($i%2).'">'; $i+=1;
		
		// Usage/Keywords
		$box .= '<div>';
		// Usage
		$box .= '<span class="usage">'.$this->getDisplayUseType().'</span>';		
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
			$box .= '<span class="range">'.$this->power_range.'</span>';
		}
		// Action Type
		$box .= '<span class="actionType">'.$this->actionTypeDisplay().'</span>';
		$box .= '</div>';
		// End Action/Range
		
		// Target
		if( !empty($this->target) ) {
			$box .= '<div><span class="target">Target: </span> '.$this->target.'</div>';
		}
		
		// Attack
		if( 'none' != $this->attack_ability ) {
			$box .= '<div><span class="attack">Attack: </span>';
			$box .= '+'.$this->attack_bonus.' ('.$this->attack_ability.')';
			$box .= 'vs. '.$this->defense.'</div>';
		}
		
		// End Statblock Row
		$box .= '</div>';
		
		// Hit
		if( !empty($this->hit) ) {
			$box .= '<div class="row'.($i%2).'" id="p'.$this->id.'hit">'; $i+=1;
			$box .= '<label>Hit: </label>';
			$box .= '<span>'.nl2br($this->hit).'</span></div>';
		}

		// Miss
		if( !empty($this->miss) ) {
			$box .= '<div class="row'.($i%2).'" id="p'.$this->id.'miss">'; $i+=1;
			$box .= '<label>Miss: </label>';
			$box .= '<span>'.nl2br($this->miss).'</span></div>';
		}
		
		// Effect
		if( !empty($this->effect) ) {
			$box .= '<div class="row'.($i%2).'" id="p'.$this->id.'effect">'; $i+=1;
			$box .= '<label>Effect: </label>';
			$box .= '<span>'.nl2br($this->effect).'</span></div>';
		}

		// Sustain
		if( 'none'!=$this->sustain_action ) {
			$box .= '<div class="row'.($i%2).'" id="p'.$this->id.'sustain">'; $i+=1;
			$box .= '<label>Sustain '.$this->sustain_action.': </label>';
			$box .= '<span>'.nl2br($this->sustain).'</span></div>';
		}
		
		// Notes
		if( !empty($this->notes) ) {
			$box .= '<div class="row'.($i%2).'" id="p'.$this->id.'notes">'; $i+=1;
			$box .= '<label>Notes: </label>';
			$box .= '<span>'.nl2br($this->notes).'</span></div>';
		}
		
		// End Description
		$box .= '</div><!-- end div.description -->';
		// End Outer Div
		$box .= '</div><!-- end div#powerBox'.$this->id.' -->';
				
		if( $echo ) echo $box;
		return $box;		
	}
}
?>
