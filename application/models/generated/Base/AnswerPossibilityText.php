<?php

/**
 * Webenq_Model_Base_AnswerPossibilityText
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $text
 * @property string $language
 * @property integer $answerPossibility_id
 * @property Webenq_Model_AnswerPossibility $AnswerPossibility
 * @property Doctrine_Collection $AnswerPossibilityTextSynonym
 * 
 * @package    Webenq
 * @subpackage Models
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 * @version    SVN: $Id: AnswerPossibilityText.php,v 1.14 2011/12/22 11:28:27 bart Exp $
 */
abstract class Webenq_Model_Base_AnswerPossibilityText extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('answerPossibilityText');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('text', 'string', 255, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => '255',
             ));
        $this->hasColumn('language', 'string', 2, array(
             'type' => 'string',
             'fixed' => 0,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => '2',
             ));
        $this->hasColumn('answerPossibility_id', 'integer', 4, array(
             'type' => 'integer',
             'fixed' => 0,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             'length' => '4',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Webenq_Model_AnswerPossibility as AnswerPossibility', array(
             'local' => 'answerPossibility_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE',
             'onUpdate' => 'CASCADE',
             'foreignKeyName' => 'answerPossibilityText_answerPossibility_id_fk'));

        $this->hasMany('Webenq_Model_AnswerPossibilityTextSynonym as AnswerPossibilityTextSynonym', array(
             'local' => 'id',
             'foreign' => 'answerPossibilityText_id'));
    }
}