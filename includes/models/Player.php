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
	
	const REST_SHORT = 'short';
	const REST_EXTENDED = 'extended';
	
	/**
	 * Maximum health for the player
	 *
	 * This is derived from the player's class, level, constitution score, and
	 * any bonus health the player may have.
	 *
	 * @var integer
	 **/
	private $health_max;
	/**
	 * Maximum Surges/Day for the player
	 *
	 * This is derived from the player's class, constitution modifier, and
	 * any bonus surges the player may have.
	 *
	 * @var integer
	 **/
	private $surges_max;
	/**
	 * Surge Value for the player
	 *
	 * This is derived from the player's maximum health, and any bonus surge value
	 * the player may have.
	 *
	 * @var integer
	 **/
	private $surge_value;
	
	/**
	 * Initialize derive values on construction
	 *
	 */
	public function construct() {
		$this->generateDerivedValues();
	}
	
	/**
	 * Re-initialize derived values after saving
	 */
	public function postSave($event) {
		$this->generateDerivedValues();
	}
	public function preInsert($event) {
		$this->magic_item_uses = 1;
		$this->initializeCurrentValues();
	}
	public function generateDerivedValues() {
		$this->health_max = $this->generateMaxHealth();
		$this->surges_max = $this->generateMaxSurges();
		$this->surge_value = $this->generateSurgeValue();		
	}

	public function initializeCurrentValues() {
		$this->health_cur = $this->generateMaxHealth();
		$this->surges_cur = $this->generateMaxSurges();
	}
	
	/**
	 * Before deleting our Player, we have to delete all our powers first.
	 */
	public function preDelete($event) {
		$this->Powers->delete();
		$this->Skills->delete();
		$this->Feats->delete();
	}

	/**
	 * @global Message
	 * @return bool
	 */
	public function updateFromForm() {
		global $msg;
		$cache = array();
		$cache['error'] = array();
				
		// Race
		$cache['race_id'] = (int)@$_POST['race'];
		$race = Doctrine::getTable('Race')->findOneById(@$_POST['race']);
		if( $race && $race->exists() ) {
			$this->Race = $race;
		}
		else {
			$msg->add('Unknown race', Message::WARNING);
			$cache['error'][] = 'race';
		}

		// Archetype (Class)
		$cache['archetype_id'] = (int)@$_POST['archetype'];
		$archetype = Doctrine::getTable('Archetype')
			->findOneByID(@$_POST['archetype']);
		if( $archetype && $archetype->exists() ) {
			$this->Archetype = $archetype;
		}
		else {
			$msg->add('Unknown class.', Message::WARNING);
			$cache['error'][] = 'archetype';
		}

		// Character Name
		$cache['name'] = $_POST['character_name'];
		if( empty($_POST['character_name']) ) {
			$msg->add('Character name may not be blank.',
				Message::WARNING);
			$cache['error'][] = 'name';
		}
		else {
			$pl = Doctrine_Query::create()->select('p.id,p.name')->from('Player p')
				->where('p.name = ?', $_POST['character_name'])
				->andWhere('(? is null or p.id != ?)', array($this->id,$this->id))
				->execute();
			if( $pl->count() > 0 ) {
				$msg->add(
					"Character name '{$_POST['character_name']}' already exists",
					Message::WARNING);
				$cache['error'][] = 'name';
			}
			else {
				$this->name = trim($_POST['character_name']);
			}
		}

		// Character Level
		$cache['level'] = $_POST['level'];
		if( empty($_POST['level']) ||
				1 > $_POST['level'] || 30 < $_POST['level'] ) {
			$msg->add('Level must be 1-30 inclusive.', Message::WARNING);
			$cache['error'][] = 'level';
		}
		else {
			$this->level = (int)$_POST['level'];
		}

		// Ability Scores
		
		// Strength
		$cache['strength'] = $_POST['strength'];
		if( empty($_POST['strength']) || $_POST['strength'] < 1 ) {
			$msg->add('Strenth must be a positive number.', Message::WARNING);
			$cache['error'][] = 'strength';
		}
		else {
			$this->strength = (int)$_POST['strength'];
		}
		
		// Dexterity
		$cache['dexterity'] = $_POST['dexterity'];
		if( empty($_POST['dexterity']) || $_POST['dexterity'] < 1 ) {
			$msg->add('Dexterity must be a positive number.', Message::WARNING);
			$cache['error'][] = 'dexterity';
		}
		else {
			$this->dexterity = (int)$_POST['dexterity'];
		}
		
		// Constitution
		$cache['constitution'] = $_POST['constitution'];
		if( empty($_POST['constitution']) || $_POST['constitution'] < 1 ) {
			$msg->add('Constitution must be a positive number.', Message::WARNING);
			$cache['error'][] = 'constitution';
		}
		else {
			$this->constitution = (int)$_POST['constitution'];
		}
		
		// Intelligence
		$cache['intelligence'] = $_POST['intelligence'];
		if( empty($_POST['intelligence']) || $_POST['intelligence'] < 1 ) {
			$msg->add('Intelligence must be a positive number.', Message::WARNING);
			$cache['error'][] = 'intelligence';
		}
		else {
			$this->intelligence = (int)$_POST['intelligence'];
		}
		
		// Wisdom
		$cache['wisdom'] = $_POST['wisdom'];
		if( empty($_POST['wisdom']) || $_POST['wisdom'] < 1 ) {
			$msg->add('Wisdom must be a positive number.', Message::WARNING);
			$cache['error'][] = 'wisdom';
		}
		else {
			$this->wisdom = (int)$_POST['wisdom'];
		}
		
		// Charisma
		$cache['charisma'] = $_POST['charisma'];
		if( empty($_POST['charisma']) || $_POST['charisma'] < 1 ) {
			$msg->add('Charisma must be a positive number.', Message::WARNING);
			$cache['error'][] = 'charisma';
		}
		else {
			$this->charisma = (int)$_POST['charisma'];
		}
		
		// General Attack Bonus
		$cache['general_attack'] = $_POST['general_attack'];
		if( !is_numeric($_POST['general_attack']) || 
				(int)$_POST['general_attack'] < 0 ) {
			$msg->add('General Attack Bonus must be a non-negative integer.', 
				Message::WARNING);
			$cache['error'][] = 'general_attack';
		}
		else {
			$this->general_attack = (int)$_POST['general_attack'];
		}
		
		// Implement Name
		$cache['implement_name'] = $_POST['implement_name'];
		$this->implement_name = trim($_POST['implement_name']);

		// Implement Attack Bonus
		$cache['implement_attack'] = $_POST['implement_attack'];
		if( !is_numeric($_POST['implement_attack']) ||
				(int)$_POST['implement_attack'] < 0 ) {
			$msg->add('Implement Attack Bonus must be a non-negative integer.', 
				Message::WARNING);
			$cache['error'][] = 'implement_attack';
		}
		else {
			$this->implement_attack = (int)$_POST['implement_attack'];
		}
		// Implement Damage Bonus
		$cache['implement_damage'] = $_POST['implement_damage'];
		if( !is_numeric($_POST['implement_damage']) ||
				(int)$_POST['implement_damage'] < 0 ) {
			$msg->add('Implement Damage Bonus must be a non-negative integer.', 
				Message::WARNING);
			$cache['error'][] = 'implement_damage';
		}
		else {
			$this->implement_damage = (int)$_POST['implement_damage'];
		}
		
		// Main Hand Weapon Info
		// Weapon name
		$cache['weapon_main_name'] = $_POST['weapon_main_name'];
		$this->weapon_main_name = trim($_POST['weapon_main_name']);
				
		// Weapon Attack Bonus
		$cache['weapon_main_attack'] = $_POST['weapon_main_attack'];
		if( !is_numeric($_POST['weapon_main_attack']) ||
				(int)$_POST['weapon_main_attack'] < 0 ) {
			$msg->add('Main Hand Weapon Attack Bonus must be a non-negative integer.', 
				Message::WARNING);
			$cache['error'][] = 'weapon_main_attack';
		}
		else {
			$this->weapon_main_attack = (int)$_POST['weapon_main_attack'];
		}
		// Weapon Damage Bonus
		$cache['weapon_main_damage'] = $_POST['weapon_main_damage'];
		if( !is_numeric($_POST['weapon_main_damage']) ||
				(int)$_POST['weapon_main_damage'] < 0 ) {
			$msg->add('Main Hand Weapon Damage Bonus must be a non-negative integer.', 
				Message::WARNING);
			$cache['error'][] = 'weapon_main_damage';
		}
		else {
			$this->weapon_main_damage = (int)$_POST['weapon_main_damage'];
		}
		// Weapon Damage Dice
		$cache['weapon_main_dice'] = $_POST['weapon_main_dice'];
		if( !empty($_POST['weapon_main_dice']) ) {
			if( FALSE === $this->parseDiceString(@$_POST['weapon_main_dice']) ) {
				$msg->add('Weapon Damage Dice must be in the form "XdY"',
					Message::WARNING);
				$cache['error'][] = 'weapon_main_dice';
			}
			else {
				$this->weapon_main_dice = $_POST['weapon_main_dice'];
			}
		}
		else {
			$this->weapon_main_dice = '1d4';
		}
		
		// Off Hand Weapon Info
		$this->weapon_off_enabled = !empty($_POST['weapon_off_enabled']);
		$cache['weapon_off_enabled'] = !empty($_POST['weapon_off_enabled']);
		if( !empty($_POST['weapon_off_enabled']) ) {
			// Weapon Name
			$cache['weapon_off_name'] = $_POST['weapon_off_name'];
			$this->weapon_off_name = trim($_POST['weapon_off_name']);
		
			// Weapon Attack Bonus
			$cache['weapon_off_attack'] = $_POST['weapon_off_attack'];
			if( !is_numeric($_POST['weapon_off_attack']) ||
					(int)$_POST['weapon_off_attack'] < 0 ) {
				$msg->add(
					'Off Hand Weapon Attack Bonus must be a non-negative integer.', 
					Message::WARNING);
				$cache['error'][] = 'weapon_off_attack';
			}
			else {
				$this->weapon_off_attack = (int)$_POST['weapon_off_attack'];
			}
		
			// Weapon Damage Bonus
			$cache['weapon_off_damage'] = $_POST['weapon_off_damage'];
			if( !is_numeric($_POST['weapon_off_damage']) ||
					(int)$_POST['weapon_off_damage'] < 0 ) {
				$msg->add(
					'Off Hand Weapon Damage Bonus must be a non-negative integer.', 
					Message::WARNING);
				$cache['error'][] = 'weapon_off_damage';
			}
			else {
				$this->weapon_off_damage = (int)$_POST['weapon_off_damage'];
			}
			// Weapon Damage Dice
			$cache['weapon_off_dice'] = $_POST['weapon_off_dice'];
			if( !empty($_POST['weapon_off_dice']) ) {
				if( FALSE === $this->parseDiceString(@$_POST['weapon_off_dice']) ) {
					$msg->add('Weapon Damage Dice must be in the form "XdY"',
						Message::WARNING);
					$cache['error'][] = 'weapon_off_dice';
				}
				else {
					$this->weapon_off_dice = trim($_POST['weapon_off_dice']);
				}
			}
			else {
				$this->weapon_off_dice = '1d4';
			}
		}

				
		// Bonus Health
		$cache['health_bonus'] = $_POST['health_bonus'];
		if( !is_numeric($_POST['health_bonus']) || 
				(int)$_POST['health_bonus'] < 0 ) {
			$msg->add('Bonus Health must be a non-negative integer.',
				Message::WARNING);
			$cache['error'][] = 'health_bonus';
		}
		else {
			$this->health_bonus = (int)$_POST['health_bonus'];
		}

		// Bonus Surges
		$cache['surges_bonus'] = $_POST['surges_bonus'];
		if( !is_numeric($_POST['surges_bonus']) || 
				(int)$_POST['surges_bonus'] < 0 ) {
			$msg->add('Bonus Surges must be a non-negative integer.',
				Message::WARNING);
			$cache['error'][] = 'surges_bonus';
		}
		else {
			$this->surges_bonus = (int)$_POST['surges_bonus'];
		}

		// Bonus Surge Value
		$cache['surge_value_bonus'] = $_POST['surge_value_bonus'];
		if( !is_numeric($_POST['surge_value_bonus']) || 
				(int)$_POST['surge_value_bonus'] < 0 ) {
			$msg->add('Bonus Surge Value must be a non-negative integer.',
				Message::WARNING);
			$cache['error'][] = 'surge_value_bonus';
		}
		else {
			$this->surge_value_bonus = (int)$_POST['surge_value_bonus'];
		}
		
		// Fortitude
		$cache['fort'] = $_POST['fort'];
		if( !is_numeric($_POST['fort']) ) {
			$msg->add('Fort bonus must be an integer.', Message::WARNING);
			$cache['error'][] = 'fort';
		}
		else {
			$this->fort = (int)$_POST['fort'];
		}
		// Reflex
		$cache['ref'] = $_POST['ref'];
		if( !is_numeric($_POST['ref']) ) {
			$msg->add('Ref bonus must be an integer.', Message::WARNING);
			$cache['error'][] = 'ref';
		}
		else {
			$this->ref = (int)$_POST['ref'];
		}
		// Will
		$cache['will'] = $_POST['will'];
		if( !is_numeric($_POST['will']) ) {
			$msg->add('Will bonus must be an integer.', Message::WARNING);
			$cache['error'][] = 'will';
		}
		else {
			$this->will = (int)$_POST['will'];
		}
		
		// Armor Class
		$cache['ac'] = $_POST['ac'];
		if( !is_numeric($_POST['ac']) ) {
			$msg->add('Armor Class bonus must be an integer.', Message::WARNING);
			$cache['error'][] = 'ac';
		}
		else {
			$this->ac = (int)$_POST['ac'];
		}
		
		// Heavy Armor
		$this->ac_heavy = !empty($_POST['ac_heavy']);

		
		// End Model Data Updates
		
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
	 * Accessor for health_max
	 * @uses $health_max
	 */
	public function getHealthMax() {
		return $this->health_max;
	}
	/**
	 * Accessor for surges_max
	 * @uses $surges_max
	 */
	public function getSurgesMax() {
		return $this->surges_max;
	}
	/**
	 * Accessor for surge_value
	 * @uses $surge_value
	 */
	public function getSurgeValue() {
		return $this->surge_value;
	}
	
	/**
	 * Generate the maximum health for the player
	 *
	 * Maximum health is calculated by summing:
	 * - The health granted at first level for the player's class
	 * - The player's constitution score
	 * - The health granted at additional levels for the player's class, times
	 * times the player's level-1
	 * - Any bonus health the player may have
	 * 
	 * @return integer
	 */
	public function generateMaxHealth() {
		$max_health = $this->Archetype->health_first + $this->constitution +
			($this->Archetype->health_level * ($this->level-1)) + 
			$this->health_bonus;
		return $max_health;
	}
	
	/**
	 * Generate the maximum surges per day for the player
	 *
	 * Surges per day is calculated by summing:
	 * - The base surges granted by the player's class
	 * - The player's constitution modifier
	 * - Any bonus surges the player may recieve
	 *
	 * @return integer
	 */
	public function generateMaxSurges() {
		$surges = $this->Archetype->surges + 
			$this->getMod('con') + $this->surges_bonus;
		return $surges;
	}
	
	/**
	 * Generate the surge value for the player
	 *
	 * Surge Value is calculated by taking one quarter of the player's
	 * maximum health and adding in any bonus surge value the player may have.
	 *
	 * @uses generateMaxHealth()
	 * @return integer
	 */
	public function generateSurgeValue() {
		$max_health = $this->generateMaxHealth();
		return floor($max_health/4) + $this->surge_value_bonus;
	}
	
	/**
	 * Retrieves the bloodied value for the player
	 *
	 * A player is bloodied at 1/2 their maximum health.
	 *
	 * @return integer
	 */
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
	 *		ie - the character's bloodied value as a negative number.
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
	
	/**
	 * Retrieve a friendly display string for the player's status
	 *
	 * @return string
	 */
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
	 * The valid parameters for this are exactly the same as getAbilityScore
	 *
	 * @uses getAbilityScore()
	 * @param string $ability The name of the ability score, eg 'strength'
	 * @return int
	 */
	public function getMod($ability, $includeLevel = false, $withSign = false) {
		$ability = $this->getAbilityScore($ability);
		if( 0 <= $ability ) {
			$mod =	floor($ability/2)-5;
		}
		else {
			$mod = 0;
		}
		
		$mod += ($includeLevel ? floor($this->level/2) : 0);
		if( $withSign && $mod >= 0 ) $mod = '+'.$mod;
		return $mod;
	}
	
	/**
	 *
	 */
	public function getDefense($def) {
		$result = 10+floor($this->level/2);
				
		switch(strtolower($def)) {
			case 'fortitude':
			case 'fort':
				$result += $this->fort;
				$result += max($this->getMod('str'),$this->getMod('con'));
				$result += $this->Archetype->fort;
				break;
			case 'reflex':
			case 'ref':
				$result += $this->ref;
				$result += max($this->getMod('dex'),$this->getMod('int'));
				$result += $this->Archetype->ref;
				break;
			case 'willpower':
			case 'will':
				$result += $this->will;
				$result += max($this->getMod('wis'),$this->getMod('cha'));
				$result += $this->Archetype->will;
				break;
			case 'armor class':
			case 'ac':
			default:
				$result += $this->ac;
				if( !$this->ac_heavy ) {
					$result += max($this->getMod('dex'),$this->getMod('int'));
				}
				break;
		}
		
		return $result;
	} 
	
	/**
	 *
	 * @param int $num Number of dice
	 * @param int $size Size of the die
	 * @return string
	 */
	public function generateDiceString($num, $size) {
		return $num.'d'.$size;		
	}
	
	/**
	 * Parses a dice string, generates an error if it cannot be parsed.
	 *
	 * @param string $string The dice string to parse
	 * @return mixed Returns an array if sucessful, FALSE otherwise
	 */
	public function parseDiceString($string) {
		$matches = array();
		if( preg_match('/(\d+)[Dd](\d+)/i', $string, $matches) ) {
			$dice = array('num' => $matches[1], 'size' => $matches[2]);
			return $dice;
		}
		return FALSE;
	}
	
	/**
	 * Returns the player's socre for the passed in ability.
	 *
	 * Valid parameters are:
	 * 'strength', 'str'
	 * 'dexterity', 'dex'
	 * 'constitution', 'con'
	 * 'intelligence, 'int'
	 * 'wisdom', 'wis'
	 * 'charisma', 'cha'
	 *
	 * @param string $ability The name of the ability score, e.g. 'strength'
	 * @return integer
	 */
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
	
	/**
	 * Perform a rest action
	 *
	 * restType should be one of the REST_* class constants.
	 *
	 * @param string $restType The type of rest to perform
	 * @return bool
	 */
	public function doRest($restType = self::REST_SHORT) {
		switch($restType) {
			case self::REST_EXTENDED:
				return $this->extendedRest();
				break;
			case self::REST_SHORT:
			default:
				return $this->shortRest();
				break;
		}
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
		
		
		// Reset magic item uses
		// Levels	 1-10 -> 1 use
		// Levels 11-20 -> 2 uses
		// Levels 21-30 -> 3 uses
		$this->magic_item_uses = floor(($this->level-1)/10)+1;
		
		// Refresh Encounter & Daily Powers
		foreach( $this->Powers as $p ) {
			$p->refresh(true);
		}
		
		return true;
	}

	/**
	 * Add a surge to our current total.
	 *
	 * Note, we cannot exceed our maximum surges
	 * @global Message
	 * @return bool
	 */
	public function addSurge() {
		global $msg;
		$error = true;
		if( $this->surges_cur >= $this->surges_max ) {
			$msg->add('You are already at maximum surges.', Message::NOTICE);
			return false;
		}
		else {
			$this->surges_cur = min($this->surges_cur+1,$this->surges_max);
			return true;
		}
	}
	
	/**
	 * Remove a surge from our current total
	 *
	 * Note, we cannot go below zero surges
	 * @global Message
	 * @return bool
	 */
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
	 * @global Message
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
	 * @global Message
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
	 * @global Message
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
	 * @return bool
	 */
	 public function addActionPoint() {
		 $this->action_points = $this->action_points+1;
		 return true;
	 }
	 
	 /**
		* Subtract an Action Point
		* 
		* @global Message
		* @return bool
		*/
	public function subtractActionPoint() {
		global $msg;
		if( $this->action_points > 0 ) {
			$this->action_points = max(0, $this->action_points-1);
			return true;
		}
		else {
			$msg->add('You have no action points remaining.', Message::WARNING);
			$this->action_points = 0;
			return false;
		}
	}
	
	/**
	 * Add a magic item use
	 *
	 * @return bool
	 */
	public function addMagicItemUse() {
		$this->magic_item_uses = $this->magic_item_uses+1;
		return true;
	}
	
	/**
	 * Subtract a magic item use
	 *
	 * @global Message
	 * @return bool
	 */
	public function subtractMagicItemUse() {
		global $msg;
		if( $this->magic_item_uses > 0 ) {
			$this->magic_item_uses = max(0, $this->magic_item_uses-1);
			return true;
		}
		else {
			$msg->add('You have no daily magic item uses remaining.',
				Message::WARNING);
			$this->magic_item_uses = 0;
			return false;
		}
	}
	
	public function activatePower($pID) {
		global $msg;
		if( !$this->hasSpellbook() ) {
			$msg->add('You do not have a spellbook', Message::WARNING);
			return false;
		}
		$power = $this->Powers->get($pID);
		if( !$power->exists() ) {
			$msg->add(sprintf("Unable to find power '%s'.", $pID), Message::ERROR);
			return false;
		}
		
		if( Power::TYPE_UTILITY == $power->power_type ) {
			$power_list = Doctrine_Query::create()
				->from('Power p')->where('p.player_id = ?', $this->id)
				->andWhere('p.power_type = ?', Power::TYPE_UTILITY)
				->andWhere('p.level = ?', $power->level)
				->execute();
			foreach($power_list as $p) {
				$p->active = ($p->id == $power->id);
				$p->save();
			}
			return array('pID' => $power->id, 'level' => $power->level);
		}
		elseif( Power::TYPE_ATTACK == $power->power_type &&
			Power::POWER_DAILY == $power->use_type ) {
			$power_list = Doctrine_Query::create()
				->from('Power p')->where('p.player_id = ?', $this->id)
				->andWhere('p.power_type = ?', Power::TYPE_ATTACK)
				->andWhere('p.use_type = ?', Power::POWER_DAILY)
				->andWhere('p.level = ?', $power->level)
				->execute();
			foreach($power_list as $p) {
				$p->active = ($p->id == $power->id);
				$p->save();
			}
			return array('pID' => $power->id, 'level' => $power->level);
		}
		else {
			$msg->add(
				"Only Utility and Daily Attack spells are part of your spellbook.",
				Message::ERROR);
			return false;
		}
	}
	
	/**
	 * Returns the attack bonus using a given accessory.
	 *
	 *
	 * @param string $accessory Accessory type to use for the attack.
	 * @return array
	 */
	public function getAttackBonus($accessory = null, $power_bonus = null) {
		$base_bonus = floor($this->level/2) + $this->general_attack;
		$base_bonus += (int)$power_bonus;
		$bonus = array();

		switch($accessory) {
			case self::ATTACK_WEAPON:
				$bonus[] = $base_bonus + $this->weapon_main_attack;
				if( $this->weapon_off_enabled ) {
					$bonus[] = $base_bonus + $this->weapon_off_attack;
				}
				break;
			case self::ATTACK_IMPLEMENT:
				$bonus[] = $base_bonus + $this->implement_attack;
				break;
			default:
				$bonus[] = $base_bonus;
				break;
		}
		return $bonus;
	}
	
	public function hasSpellbook() {
		switch($this->Archetype->name) {
			case 'Wizard':
				return true;
				break;
			default:
				return false;
				break;
		}
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
	
	public function hasError($field, $form_key = null) {
		return ( $this->contains($field) && !empty($form_key) && 
			!empty($_SESSION['form_cache']) && 
			$_SESSION['form_cache']['form_key'] == $form_key &&
			!empty($_SESSION['form_cache']['error']) &&
			in_array($field, $_SESSION['form_cache']['error']) ); 
	}
}

?>
