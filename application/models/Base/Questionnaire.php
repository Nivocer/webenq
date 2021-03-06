<?php

/**
 * Webenq_Model_Base_Questionnaire
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $title
 * @property integer $category_id
 * @property string $default_language
 * @property timestamp $date_start
 * @property timestamp $date_end
 * @property integer $active
 * @property string $meta
 * @property int $weight
 * @property integer $questionnaire_node_id
 * @property Webenq_Model_Category $Category
 * @property Webenq_Model_QuestionnaireNode $QuestionnaireNode
 * @property Doctrine_Collection $QuestionnaireQuestion
 * @property Doctrine_Collection $Report
 * @property Doctrine_Collection $Respondent
 * 
 * @package    Webenq_Models
 * @subpackage 
 * @author     Nivocer <webenq@nivocer.com>
 * @version    SVN: $Id: Builder.php,v 1.2 2011/07/12 13:39:03 bart Exp $
 */
abstract class Webenq_Model_Base_Questionnaire extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('questionnaire');
        $this->hasColumn('title', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('category_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('default_language', 'string', 2, array(
             'type' => 'string',
             'fixed' => 0,
             'default' => 'en',
             'notnull' => true,
             'length' => '2',
             ));
        $this->hasColumn('date_start', 'timestamp', 25, array(
             'type' => 'timestamp',
             'default' => '2012-01-01 00:00:00',
             'notnull' => true,
             'length' => '25',
             ));
        $this->hasColumn('date_end', 'timestamp', 25, array(
             'type' => 'timestamp',
             'default' => '2050-01-01 00:00:00',
             'notnull' => true,
             'length' => '25',
             ));
        $this->hasColumn('active', 'integer', 1, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => false,
             'default' => '1',
             'notnull' => true,
             'length' => '1',
             ));
        $this->hasColumn('meta', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'length' => '',
             ));
        $this->hasColumn('weight', 'int', null, array(
             'type' => 'int',
             ));
        $this->hasColumn('questionnaire_node_id', 'integer', null, array(
             'type' => 'integer',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Webenq_Model_Category as Category', array(
             'local' => 'category_id',
             'foreign' => 'id',
             'onDelete' => 'RESTRICT',
             'onUpdate' => 'CASCADE'));

        $this->hasOne('Webenq_Model_QuestionnaireNode as QuestionnaireNode', array(
             'local' => 'questionnaire_node_id',
             'foreign' => 'id'));

        $this->hasMany('Webenq_Model_QuestionnaireQuestion as QuestionnaireQuestion', array(
             'local' => 'id',
             'foreign' => 'questionnaire_id'));

        $this->hasMany('Webenq_Model_Report as Report', array(
             'local' => 'id',
             'foreign' => 'questionnaire_id'));

        $this->hasMany('Webenq_Model_Respondent as Respondent', array(
             'local' => 'id',
             'foreign' => 'questionnaire_id'));

        $webenq4_template_i18n0 = new WebEnq4_Template_I18n(array(
             'fields' => 
             array(
              0 => 'title',
             ),
             ));
        $this->actAs($webenq4_template_i18n0);
    }
}