<?php

/**
 * Webenq_Model_Base_Category
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $active
 * @property integer $weight
 * @property string $text
 * @property Doctrine_Collection $Questionnaire
 * 
 * @package    Webenq_Models
 * @subpackage 
 * @author     Nivocer <webenq@nivocer.com>
 * @version    SVN: $Id: Builder.php,v 1.2 2011/07/12 13:39:03 bart Exp $
 */
abstract class Webenq_Model_Base_Category extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('category');
        $this->hasColumn('active', 'integer', 1, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'default' => '1',
             'notnull' => true,
             'length' => '1',
             ));
        $this->hasColumn('weight', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'default' => 0,
             'notnull' => true,
             'length' => '4',
             ));
        $this->hasColumn('text', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Webenq_Model_Questionnaire as Questionnaire', array(
             'local' => 'id',
             'foreign' => 'category_id'));

        $webenq4_template_i18n0 = new WebEnq4_Template_I18n(array(
             'fields' => 
             array(
              0 => 'text',
             ),
             ));
        $this->actAs($webenq4_template_i18n0);
    }
}