<?php
/**
 * Power
 * 
 * @author Jonathan Borzilleri
 */
class Power extends BasePower {
	private $tmp_keywords = array();

	const POWER_ATWILL = '1_atwill';
	const POWER_ENCOUNTER = '2_encounter';
	const POWER_DAILY = '3_daily';
	const POWER_SURGE = '4_surge';
	
	const TYPE_ATTACK = 'attack';
	const TYPE_UTILITY = 'utility';
	const TYPE_RACIAL = 'racial';
	const TYPE_ITEM = 'item';
	const TYPE_OTHER = 'other';
	
	const CHARGE_ENCOUNTER = 'encounter';
	const CHARGE_DAILY = 'daily';
	const CHARGE_CONSUMABLE = 'consumable';
	const CHARGE_NONE = 'none';
	
	const STATUS_USED = 'Used';
	const STATUS_DISABLED = 'Disabled';
	
	const ICON_USABLE = '/images/icon_zap.png';
	const ICON_USED = '/images/icon_refresh.png';
	const ICON_DISABLED = '/images/icon_skull.png';

	/**
	 * Pre-Delete handling, we must delete our keyword associations first.
	 */
	public function preDelete($event) {
		$this->PowerKeywords->delete();
	}
	
	/**
	 * Post-save handling, update our keywords appropriately.
	 */
	public function postSave($event) {
		// After saving the Power, we need to go through the $tmp_keywords array
		// and update any objects there with our ID, and save them.
		foreach($this->tmp_keywords as $k) {
			$k->power_id = $this->id;
			$k->save();
		}
	}

	public function gethas_charges() {
		return CHARGE_NONE != $this->charge_type;
	}

	public function getCharges() {
		switch($this->charge_type) {
			case CHARGE_ENCOUNTER:
			case CHARGE_DAILY:
				return $this->uses_max;
				break;
			case CHARGE_CONSUMABLE:
				return min(0,$this->uses);
				break;
			default:
				return false;
				break;
		}
	}
	
	public function getUsed() {
		return ($this->used<=0);
	}
	
	/**
	 *
	 * @global Message
	 * @return bool
	 */
	public function updateFromForm() {
		global $msg;
		$cache = array();
		$cache['keywords'] = array();
		$cache['error'] = array();
		
		// Power Name
		$cache['name'] = $_POST['power_name'];
		if( empty($_POST['power_name']) ) {
			$msg->add('Name must not be empty.', Message::WARNING);
			$cache['error'][] = 'name';
		}
		else {
			$this->name = trim($_POST['power_name']);
		}
		
		// Level
		$cache['level'] = $_POST['level'];
		if( empty($_POST['level']) ||
				1 > $_POST['level'] || 30 < $_POST['level'] ) {
			$msg->add('Level must be between 1 and 30.', Message::WARNING);
			$cache['error'][] = 'level';
		}
		else {
			$this->level = (int)$_POST['level'];
		}
		
		// PowerType
		$cache['power_type'] = $_POST['power_type'];
		if( empty($_POST['power_type']) ||
			!$this->isValidPowerType($_POST['power_type']) ) {
			$msg->add("Invalid power type.", Message::WARNING);
			$cache['error'][] = 'power_type';
		}
		else {
			$this->power_type = $_POST['power_type'];
		}
		
		// UseType
		$cache['use_type'] = $_POST['use_type'];
		if( empty($_POST['use_type']) || 
				!$this->isValidUseType($_POST['use_type']) ) {
			$msg->add('Usage Type must be "at-will", "encounter", or "daily."',
				Message::WARNING);
			$cache['error'][] = 'use_type';
		}
		else {
			$this->use_type = $_POST['use_type'];
		}
		
		// Action Type
		$cache['action_type'] = $_POST['action_type'];
		if( empty($_POST['action_type']) || 
				!$this->isValidActionType($_POST['action_type']) ) {
			$msg->add('Invalid action type.', Message::WARNING);
			$cache['error'][] = 'action_type';
		}
		else {
			$this->action = $_POST['action_type'];
		}
		
		// Attack Stat
		$cache['attack_ability'] = $_POST['attack_ability'];
		if( empty($_POST['attack_ability']) || 
				!$this->isValidAttackStat($_POST['attack_ability']) ) {
			$msg->add('Invalid Attack Stat', Message::WARNING);
			$cache['error'][] = 'attack_ability';
		}
		else {
			$this->attack_ability = $_POST['attack_ability'];
		}
		
		// Attack Defense
		$cache['defense'] = $_POST['defense'];
		if( empty($_POST['defense']) ||
				!$this->isValidDefense($_POST['defense']) ) {
			$msg->add('Invalid Defense', Message::WARNING);
			$cache['error'][] = 'defense';
		}
		else {
			$this->defense = $_POST['defense'];
		}

		// Attack Bonus
		$cache['attack_bonus'] = $_POST['attack_bonus'];
		if( !empty($_POST['attack_bonus']) && !is_numeric($_POST['attack_bonus'])) {
			$msg->add('Attack Bonus must be an integer.', Message::WARNING);
			$cache['error'][] = 'attack_bonus';
		}
		else {
			$this->attack_bonus = (int)trim(@$_POST['attack_bonus']);
		}
		
		// Sustain Action
		$cache['sustain_action'] = $_POST['sustain_action'];
		$cache['sustain'] = $_POST['sustain'];
		if( empty($_POST['sustain_action']) ||
				!$this->isValidSustainAction($_POST['sustain_action']) ) {
			$msg->add('Invalid Sustain Action', Message::WARNING);
			$cache['error'][] = 'sustain_action';
		}
		else {
			$this->sustain_action = $_POST['sustain_action'];
			$this->sustain = trim(@$_POST['sustain']);
		}

		// Charge type
		$cache['has_charges'] = !empty($_POST['has_charges']);
		if( !empty($_POST['has_charges']) ) {
			$cache['charges'] = @$_POST['charges'];
			$cache['charge_type'] = @$_POST['charge_type'];
			if( !$this->isValidChargeType($_POST['charge_type']) ) {
				$msg->add('Invalid Charge Type', Message::WARNING);
				$cache['error'][] = 'charge_type';
			}
			elseif( empty($_POST['charges']) || !is_numeric($_POST['charges']) ||
				0 >= (int)$_POST['charges'] ) {
				$msg->add('Number of charges must be a non-negative integer and cannot be blank.', Message::WARNING);
				$cache['error'][] = 'charges';
			}
			else {
				$this->charge_type = $_POST['charge_type'];
				$this->uses = $_POST['charges'];
				$this->uses_max = ($this->charge_type == self::CHARGE_CONSUMABLE)?
					0 : $_POST['charges'];
			}
		}
		else {
			$this->charge_type = self::CHARGE_NONE;
			$this->uses = 1;
			$this->uses_max = 1;
		}
		
		$this->target = trim(@$_POST['target']);
		$cache['target'] = $_POST['target'];

		$this->power_range = trim(@$_POST['power_range']);
		$cache['power_range'] = $_POST['power_range'];

		$this->hit = trim(@$_POST['hit']);
		$cache['hit'] = $_POST['hit'];
		
		$this->miss = trim(@$_POST['miss']);
		$cache['miss'] = $_POST['miss'];

		$this->effect = trim(@$_POST['effect']);
		$cache['effect'] = $_POST['effect'];

		$this->notes = trim(@$_POST['notes']);
		$cache['notes'] = $_POST['notes'];
		
		if( !$this->exists() ) {
			$this->active = !($this->Player->hasSpellbook() && 
				(self::TYPE_UTILITY == $this->power_type ||
					(self::TYPE_ATTACK == $this->power_type && 
					self::POWER_DAILY == $this->use_type)	) );
		}
				
		// Power Keywords
		if( !empty($_POST['keywords']) && is_array($_POST['keywords']) ) {
			// First: Iterate through the _POST keyword array
			// For any keyword we don't already have, make a new PowerKeyword object
			// and add it to the tmp_keywords array
			foreach($_POST['keywords'] as $k_id) {
				$cache['keywords'][$k_id] = true;
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

		// Update the form cache in the session if necessary.
		if( !empty($_POST['form_key']) ) {
			if( empty($cache['error']) ) {
				unset($_SESSION[$_POST['form_key']]);
			}
			else {
				$cache['form_key'] = $_POST['form_key'];
				$_SESSION['form_cache'] = $cache;
			}
		}

		return empty($cache['error']);
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
			case 'Standard':
			case 'Move':
			case 'Minor':
			case 'Free':
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
			case self::POWER_SURGE;
				return true;
				break;
			default:
				return false;
				break;
		}
	}
	
	public function isValidPowerType($type) {
		switch($type) {
			case self::TYPE_ATTACK:
			case self::TYPE_UTILITY:
			case self::TYPE_RACIAL:
			case self::TYPE_ITEM:
			case self::TYPE_OTHER:
				return true;
				break;
			default:
				return false;
				break;
		}
	}

	protected function isValidChargeType($type) {
		switch($type) {
			case self::CHARGE_ENCOUNTER:
			case self::CHARGE_DAILY:
			case self::CHARGE_CONSUMABLE:
			case self::CHARGE_NONE:
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
	 * @param bool $noSpace Strip spaces out of the returned string
	 * @return string
	 */
	public function getUseTypeDisplay($noSpace = false) {
		$s = '';
		switch($this->use_type) {
			case self::POWER_SURGE:
				$s = 'Healing Surge';
				break;
			case self::POWER_ENCOUNTER:
				$s = 'Encounter';
				break;
			case self::POWER_DAILY:
				$s = 'Daily';
				break;
			case self::POWER_ATWILL:
			default:
				$s = 'At-Will';
				break;
		}
		
		if( $noSpace ) $s = str_replace(' ', '-', $s);
		return $s;
	}
	
	/**
	 * Refresh the power, allowing it to be used again.
	 *
	 * @param bool $overrideCost Override any healing surge requirement
	 * @return bool
	 */ 
	public function refresh($overrideCost = false) {
		if( !self::CHARGE_CONSUMABLE != $this->charge_type ) {
			$recharge = (!$this->isUseType(self::POWER_SURGE) || $overrideCost) ?
				true : $this->Player->subtractSurge();
			
			if( $recharge ) {
				$this->uses = $this->uses_max;
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Use a power, expending it.
	 *
	 * At-Will Powers cannot be expended.
	 *
	 * @param bool #overrideCost Override any cost for using the power.
	 * @return bool
	 */
	public function usePower($overrideCost = false) {
		global $msg;
		if( !$this->isActive() ) {
			$msg->add('You can not use inactive powers.', Message::WARNING);
		}
		elseif( !$overrideCost && self::TYPE_ITEM == $this->power_type &&
				self::POWER_DAILY == $this->use_type ) {
			// We're a magic item daily ability, 
			// so on use we must remove a magic item usage
			if( $this->Player->subtractMagicItemUse() ) {
				$this->uses -= 1;
				$this->uses = max($this->uses,0);
				return true;
			}
		}
		elseif( self::CHARGE_CONSUMABLE == $this->charge_type || 
			self::POWER_ATWILL != $this->use_type ) {
			$this->uses -= 1;
			$this->uses = max($this->uses,0);
			return true;
		}
		return false;
	}
	
	/**
	 * Toggle a power's used state.
	 *
	 * At-Will Powers cannot be toggled.
	 * 
	 * @param bool $overrideCost Override any cost for using/refreshing the power
	 * @return bool
	 */
	public function togglePower($overrideCost = false) {
		if( $this->isUsable() ) return $this->usePower($overrideCost);
		else return $this->refresh($overrideCost);
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
			case 'power_range':
				$out = preg_replace('/(melee)/i', '<label>$1</label>',$out);
				$out = preg_replace('/(ranged)/i', '<label>$1</label>',$out);
				$out = preg_replace('/(area)/i', '<label>$1</label>',$out);
				$out = preg_replace('/(close)/i', '<label>$1</label>',$out);
				$out = preg_replace('/(personal)/i', '<label>$1</label>',$out);
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
				// Trigger
				$out = preg_replace('/(trigger:)/i', '<label>$1</label>', $out);
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
		else {
			return $this->Player->getAttackBonus(null, $power_bonus);
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
	
	public static function typeDisplay($power_type) {
		switch($power_type) {
			case self::TYPE_ITEM:
				return 'Magic Item';
				break;
			case self::TYPE_RACIAL:
				return 'Racial';
				break;
			case self::TYPE_ATTACK:
				return 'Attack';
				break;
			case self::TYPE_UTILITY:
				return 'Utility';
				break;
			default:
				return 'Other';
				break;
		}	
	}
	
	public static function chargeTypeDisplay($charge_type) {
		switch($charge_type) {
			case self::CHARGE_ENCOUNTER:
				return 'per Encounter';
				break;
			case self::CHARGE_DAILY:
				return 'per Day';
				break;
			case self::CHARGE_CONSUMABLE:
				return 'Consumable';
				break;
			default:
				return '';
				break;
		}
	}
	
	/**
	 *
	 */
	public function getClassDisplay() {
		switch($this->power_type) {
			case self::TYPE_ITEM:
				return 'Magic Item';
				break;
			case self::TYPE_RACIAL:
				return $this->Player->Race->name;
				break;
			case self::TYPE_ATTACK:
			case self::TYPE_UTILITY:
			default:
				return $this->Player->Archetype->name;
				break;
		}
	}
	
	/**
	 *
	 */
	public function isUsable() {
		return ($this->uses > 0);
	}
	
	/**
	 *
	 */
	public function isDisabled() {
		return self::TYPE_ITEM == $this->power_type && 
			$this->use_type == self::POWER_DAILY && 
			0 == $this->Player->magic_item_uses;
	}
	
	public function isActive() {
		if( self::TYPE_UTILITY == $this->power_type ) {
			return (bool)$this->active;
		}
		elseif( self::TYPE_ATTACK == $this->power_type && 
			self::POWER_DAILY == $this->use_type ) {
			return (bool)$this->active;
		}
		else {
			return true;
		}
	}
	
	public function getUsageStatus() {
		if( $this->uses <= 0 ) {
			return self::STATUS_USED;
		}
		elseif( $this->isDisabled() ) {
			return self::STATUS_DISABLED;
		}
		return '';
	}
	
	public function getUsageIcon() {
		if( $this->uses <= 0 ) {
			return self::ICON_USED;
		}
		elseif( $this->isDisabled() ) {
			return self::ICON_DISABLED;
		}
		else {
			return self::ICON_USABLE;
		}
	}
	
	public function isSpellbookPower() {
		return (self::TYPE_UTILITY == $this->power_type ||
			(self::TYPE_ATTACK == $this->power_type && 
			self::POWER_DAILY == $this->use_type));
	}
	
	/**
	 * Retrieve a cached value for a property if one exists. 
	 *
	 * If a field is passed in that is not contained in the object,
	 * we return false.
	 *
	 * @param string $field Property name to retrieve
	 * @param string $form_key Key name for the form cache to check
	 * @return mixed The cached value, or the internal value if no cached value
	 */
	public function getCached($field, $form_key = null) {
		if( $this->contains($field) ) {
			if( !empty($form_key) && !empty($_SESSION['form_cache']) &&
					$_SESSION['form_cache']['form_key'] == $form_key &&
					array_key_exists($field, $_SESSION['form_cache']) ) {
				return $_SESSION['form_cache'][$field];
			}
			else {
				return $this->$field;
			}
		}
		return false;
	}
	
	public function hasCachedKeyword($kw_id, $form_key = null) {
		if( !empty($form_key) && !empty($_SESSION['form_cache']) &&
				$_SESSION['form_cache']['form_key'] == $form_key ) {
			return array_key_exists($kw_id, $_SESSION['form_cache']['keywords']);
		}
		
		return $this->Keywords->contains($kw_id);
	}
	
	public function hasError($field, $form_key = null) {
		return ( $this->contains($field) && !empty($form_key) && 
			!empty($_SESSION['form_cache']) && 
			$_SESSION['form_cache']['form_key'] == $form_key &&
			!empty($_SESSION['form_cache']['error']) &&
			in_array($field, $_SESSION['form_cache']['error']) );
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
	 *	bar based on the used property.
	 * @param bool $echo Whether to print the generated box or return it.
	 * @return string
	 */
	public function getPowerBoxDisplay($collapseUsed = false, $echo = false) {
		$box = ""; $i = 0;
		
		// Outer div
		$box .= '<div class="powerBox'.($this->isDisabled()?' disabledPower':'')
			.'" id="powerBox'.$this->id.'">';
		
		// TitleBar
		$box .= '<div id="p'.$this->id.'" class="titleBar '.
			$this->getUseTypeDisplay(true).'-bg">';
		// Power Class/Level
		$box .= '<span class="powerLevel">'.$this->getClassDisplay().
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
			$box .= '<span>'
				.$this->scanAndParseText($this->getTextFieldDisplay('hit'))
				.'</span></div>';
		}

		// Miss
		if( !empty($this->miss) ) {
			$box .= '<div class="row'.($i%2).'" id="p'.$this->id.'miss">'; $i+=1;
			$box .= '<label>Miss: </label><span>';
			$box .= '<span>'
				.$this->scanAndParseText($this->getTextFieldDisplay('miss'))
				.'</span></div>';
		}
		
		// Effect
		if( !empty($this->effect) ) {
			$box .= '<div class="row'.($i%2).'" id="p'.$this->id.'effect">'; $i+=1;
			$box .= '<label>Effect: </label>';
			$box .= '<span>'
				.$this->scanAndParseText($this->getTextFieldDisplay('effect'))
				.'</span></div>';
		}

		// Sustain
		if( 'none'!=$this->sustain_action ) {
			$box .= '<div class="row'.($i%2).'" id="p'.$this->id.'sustain">'; $i+=1;
			$box .= '<label>Sustain '.$this->sustain_action.': </label>';
			$box .= '<span>'
				.$this->scanAndParseText($this->getTextFieldDisplay('sustain'))
				.'</span></div>';
		}
		
		// Notes
		if( !empty($this->notes) ) {
			$box .= '<div class="row'.($i%2).'" id="p'.$this->id.'notes">'; $i+=1;
			$box .= '<label>Notes: </label>';
			$box .= '<span>'
				.$this->scanAndParseText($this->getTextFieldDisplay('notes'))
				.'</span></div>';
		}
		
		// End Description
		$box .= '</div><!-- end div.description -->';
		// End Outer Div
		$box .= '</div><!-- end div#powerBox'.$this->id.' -->';
		$box .= "\n\n";
				
		if( $echo ) echo $box;
		return $box;		
	}
	
	/**
	 *
	 */
	public function getStringReplacement($string, $multiplier = 1) {
		$result = '';
		switch(strtolower($string)) {
			// Main Hand Base Weapon Damage
			case 'mw':
				$dice = $this->Player->parseDiceString($this->Player->weapon_main_dice);
				$result = ($dice['num']*$multiplier).'d'.$dice['size'];
				break;
			// Off Hand Base Weapon Damage
			case 'ow':
				$dice = $this->Player->parseDiceString($this->Player->weapon_off_dice);
				$result = ($dice['num']*$multiplier).'d'.$dice['size'];
				break;
			// Ability Modifier tags
			case 'str':
			case 'con':
			case 'dex':
			case 'int':
			case 'wis':
			case 'cha':
				$result = $this->Player->getMod($string);
				$result = $result * $multiplier;
				break;
			// Main Hand Bonus Damage
			case 'mdam':
				$result = $this->Player->weapon_main_damage;
				$result = $result * $multiplier;
				break;
			// Off Hand Bonus Damage
			case 'odam':
				$result = $this->Player->weapon_off_damage;
				$result = $result * $multiplier;
				break;
			// Implement Bonus Damage
			case 'idam':
				$result = $this->Player->implement_damage;
				$result = $result * $multiplier;
				break;
			case 'surge':
				$result = $this->Player->surge_value;
				$result = $result * $multiplier;
				break;
			case 'lvl':
				$result = $this->Player->level;
				$result = $result * $multiplier;
				break;
			case 'hlvl':
				$result = floor($this->Player->level/2);
				$result = $result * $multiplier;
				break;
		}
		return $result;
	}

	
	/**
	 *
	 */
	public function parseTag($tag) {
		$result = '';
		$inner_tag = substr($tag, 1, -1);
		preg_match_all('/[\+\-]?\d*\w*/i', $inner_tag, $matches);
		foreach($matches[0] as $k => $s) {
			// Don't operate on 'empty' strings
			if( !empty($s) ) {
				// Figure out what operation we're doing
				// Addition (+) or subtraction (-);
				$op = substr($s,0,1);
				if( '+' == $op || '-' == $op ) {
					$textString = substr($s, 1);
				}
				else {
					$textString = $s;
					$op = '+';
				}
				// Separate our multiplier or constant from the text string
				preg_match('/(\d*)(\w*)/i', $textString, $parts);
				$multiplier = empty($parts[1])?1:$parts[1];
				$textString = $parts[2];
				
				// Grab the replaced string
				if( empty($textString) ) {
					$temp_result = $multiplier;
				}
				else {
					$temp_result = $this->getStringReplacement(
						$textString, $multiplier);
				}
				
				// Integrate this string with the overall tag result
				// If we're the first one, just set the result
				if( 0 == $k ) {
					$result = $temp_result;
				}
				elseif( is_numeric($result) ) {
					// Otherwise, if we're numeric, add or subtract it appropriately
					if( '+' == $op ) {
						$result += $temp_result;
					}
					elseif( '-' == $op ) {
						$result -= $temp_result;
					}
				}
			} // End if(empty($s))
		} // endforeach
		return $result;
	}
	
	public function scanAndParseText($text) {
		$result = $text;
		if( preg_match_all('/(\[.*?\])/i', $text, $matches) ) {
			foreach($matches[1] as $tag) {
				$result = str_replace($tag, $this->parseTag($tag), $result);
			}
		}
		return $result;
	}
}
?>
