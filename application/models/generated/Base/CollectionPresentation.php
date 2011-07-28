<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Webenq_Model_CollectionPresentation', 'doctrine');

/**
 * Webenq_Model_Base_CollectionPresentation
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $parent_id
 * @property integer $questionnaire_question_id
 * @property string $type
 * @property string $filters
 * @property string $validators
 * @property integer $page
 * @property integer $weight
 * @property Webenq_Model_QuestionnaireQuestion $QuestionnaireQuestion
 * 
 * @package    Webenq
 * @subpackage Models
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: CollectionPresentation.php,v 1.1 2011/07/12 12:14:19 bart Exp $
 */
abstract class Webenq_Model_Base_CollectionPresentation extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('collectionPresentation');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('parent_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('questionnaire_question_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('type', 'string', 64, array(
             'type' => 'string',
             'length' => 64,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('filters', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('validators', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('page', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '1',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('weight', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Webenq_Model_QuestionnaireQuestion as QuestionnaireQuestion', array(
             'local' => 'questionnaire_question_id',
             'foreign' => 'id'));
    }
}