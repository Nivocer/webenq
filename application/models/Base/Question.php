<?php

/**
 * Webenq_Model_Base_Question
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property timestamp $created
 * @property Doctrine_Collection $QuestionText
 * @property Doctrine_Collection $QuestionnaireQuestion
 * 
 * @package    Webenq_Models
 * @subpackage 
 * @author     Nivocer <webenq@nivocer.com>
 * @version    SVN: $Id: Builder.php,v 1.2 2011/07/12 13:39:03 bart Exp $
 */
abstract class Webenq_Model_Base_Question extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('question');
        $this->hasColumn('created', 'timestamp', 25, array(
             'type' => 'timestamp',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => true,
             'length' => '25',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Webenq_Model_QuestionText as QuestionText', array(
             'local' => 'id',
             'foreign' => 'question_id'));

        $this->hasMany('Webenq_Model_QuestionnaireQuestion as QuestionnaireQuestion', array(
             'local' => 'id',
             'foreign' => 'question_id'));
    }
}