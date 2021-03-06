<?php

/**
 * Webenq_Model_Base_MigrationVersion
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $version
 * 
 * @package    Webenq_Models
 * @subpackage 
 * @author     Nivocer <webenq@nivocer.com>
 * @version    SVN: $Id: Builder.php,v 1.2 2011/07/12 13:39:03 bart Exp $
 */
abstract class Webenq_Model_Base_MigrationVersion extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('migration_version');
        $this->hasColumn('version', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'length' => '4',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}