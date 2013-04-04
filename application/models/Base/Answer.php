<?php

/**
 * Webenq_Model_Base_Answer
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $answerPossibility_id
 * @property string $text
 * @property integer $respondent_id
 * @property integer $questionnaire_question_id
 * @property timestamp $timestamp
 * @property Webenq_Model_AnswerPossibility $AnswerPossibility
 * @property Webenq_Model_QuestionnaireQuestion $QuestionnaireQuestion
 * @property Webenq_Model_Respondent $Respondent
 * 
 * @package    Webenq_Models
 * @subpackage 
 * @author     Nivocer <webenq@nivocer.com>
 * @version    SVN: $Id: Builder.php,v 1.2 2011/07/12 13:39:03 bart Exp $
 */
abstract class Webenq_Model_Base_Answer extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('answer');
        $this->hasColumn('answerPossibility_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('text', 'string', null, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'notnull' => false,
             'length' => '',
             ));
        $this->hasColumn('respondent_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('questionnaire_question_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('timestamp', 'timestamp', 25, array(
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
        $this->hasOne('Webenq_Model_AnswerPossibility as AnswerPossibility', array(
             'local' => 'answerPossibility_id',
             'foreign' => 'id',
             'onDelete' => 'RESTRICT',
             'onUpdate' => 'RESTRICT',
             'foreignKeyName' => 'answer_answerPossibility_answerPossibility_id_fk'));

        $this->hasOne('Webenq_Model_QuestionnaireQuestion as QuestionnaireQuestion', array(
             'local' => 'questionnaire_question_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE',
             'onUpdate' => 'CASCADE',
             'foreignKeyName' => 'answer_questionnaire_question_id_fk'));

        $this->hasOne('Webenq_Model_Respondent as Respondent', array(
             'local' => 'respondent_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE',
             'onUpdate' => 'CASCADE',
             'foreignKeyName' => 'answer_respondent_id_fk'));
    }
}