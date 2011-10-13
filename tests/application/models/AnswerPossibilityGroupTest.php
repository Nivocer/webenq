<?php
class Webenq_Test_Model_AnswerPossibilityGroupTest extends Webenq_Test_Case_Model
{
    public function testAnswerPossibilityGroupIsFound()
    {
        $values = array('', 'mee eens', 'geheel mee eens', 'neutraal');
        $group = Webenq_Model_AnswerPossibilityGroup::findByAnswerValues($values, 'nl');
        $this->assertTrue($group instanceof Webenq_Model_AnswerPossibilityGroup);
    }

    public function testAnswerPossibilityGroupIsCreated()
    {
        $values = array('', 'mee eens', 'geheel mee eens', 'neutraal', 'helemaal mee oneens');
        $group = Webenq_Model_AnswerPossibilityGroup::createByAnswerValues($values, 'nl');
        $this->assertTrue($group instanceof Webenq_Model_AnswerPossibilityGroup);
    }
}