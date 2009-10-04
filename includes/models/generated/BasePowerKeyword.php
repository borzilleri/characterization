<?php

/**
 * BasePowerKeyword
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $power_id
 * @property integer $keyword_id
 * @property Power $Power
 * @property Keyword $Keyword
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
abstract class BasePowerKeyword extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('power_keyword');
        $this->hasColumn('id', 'integer', 8, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'unsigned' => '1',
             'length' => '8',
             ));
        $this->hasColumn('power_id', 'integer', 8, array(
             'type' => 'integer',
             'unsigned' => '1',
             'length' => '8',
             ));
        $this->hasColumn('keyword_id', 'integer', 8, array(
             'type' => 'integer',
             'unsigned' => '1',
             'length' => '8',
             ));

        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
    $this->hasOne('Power', array(
             'local' => 'power_id',
             'foreign' => 'id'));

        $this->hasOne('Keyword', array(
             'local' => 'keyword_id',
             'foreign' => 'id'));
    }
}