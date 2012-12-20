<?php
class Webenq_Test_Model_AnswerPossibilityGroupTest extends Webenq_Test_Case_Model
{
    public function testAnswerPossibilityGroupIsCreated()
    {
        $values = array('', 'mee eens', 'geheel mee eens', 'neutraal', 'helemaal mee oneens');
        $group = Webenq_Model_AnswerPossibilityGroup::createByAnswerValues($values, 'nl');
        $this->assertTrue($group instanceof Webenq_Model_AnswerPossibilityGroup);
    }

    public function testAnswerPossibilityGroupIsFound()
    {
        $testValues = array(
            array('', 'mee eens', 'helemaal mee eens', 'neutraal'),
            array('', 'Weet niet / NvT', 'mee eens', 'helemaal mee eens', 'neutraal'),
        );
        foreach ($testValues as $values) {
            $create = Webenq_Model_AnswerPossibilityGroup::createByAnswerValues($values, 'nl');
        };

        foreach ($testValues as $values) {
            $group = Webenq_Model_AnswerPossibilityGroup::findByAnswerValues($values, 'nl');
            $this->assertTrue($group instanceof Webenq_Model_AnswerPossibilityGroup);
        }
    }
}
