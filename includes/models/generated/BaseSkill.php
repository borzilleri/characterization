<?php

/**
 * BaseSkill
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property enum $ability
 * @property boolean $trained
 * @property integer $bonus
 * @property integer $player_id
 * @property Player $Player
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
abstract class BaseSkill extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('skill');
        $this->hasColumn('id', 'integer', 8, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'unsigned' => '1',
             'length' => '8',
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('ability', 'enum', null, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => 'Str',
              1 => 'Dex',
              2 => 'Con',
              3 => 'Int',
              4 => 'Wis',
              5 => 'Cha',
             ),
             ));
        $this->hasColumn('trained', 'boolean', null, array(
             'type' => 'boolean',
             'default' => false,
             ));
        $this->hasColumn('bonus', 'integer', null, array(
             'type' => 'integer',
             'default' => 0,
             ));
        $this->hasColumn('player_id', 'integer', null, array(
             'type' => 'integer',
             'unsigned' => '1',
             ));

        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
    $this->hasOne('Player', array(
             'local' => 'player_id',
             'foreign' => 'id'));
    }
}