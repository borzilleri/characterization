<?php

/**
 * Skill
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package		 ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author		 ##NAME## <##EMAIL##>
 * @version		 SVN: $Id: Builder.php 5845 2009-06-09 07:36:57Z jwage $
 */
class Skill extends BaseSkill {
	const TRAINED_BONUS = 5;
	
	/**
	 *
	 */
	public function getMod($withSign = true) {
		$mod = $this->Player->getMod($this->ability) +
			($this->trained ? self::TRAINED_BONUS : 0) +
			($this->bonus);
		
		if( 0 <= $mod && $withSign ) {
			$mod = '+'.$mod;
		}
		return $mod;
	}
}