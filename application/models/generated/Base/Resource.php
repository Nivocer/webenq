<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Webenq_Model_Resource', 'doctrine');

/**
 * Webenq_Model_Base_Resource
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property Doctrine_Collection $RoleResource
 * 
 * @package    Webenq
 * @subpackage Models
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Resource.php,v 1.1 2011/07/12 12:14:20 bart Exp $
 */
abstract class Webenq_Model_Base_Resource extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('resource');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', 64, array(
             'type' => 'string',
             'length' => 64,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Webenq_Model_RoleResource as RoleResource', array(
             'local' => 'id',
             'foreign' => 'resource_id'));
    }
}