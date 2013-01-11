<?php
class Webenq_Test_Model_QuestionTest extends Webenq_Test_Case_Model_Question
{
    public function testAnswersCanBeAddedToQuestion()
    {
        $question = new Webenq_Model_Question();
        $question->setAnswerValues(array('answer 1', 'answer 2'));
        $this->assertTrue(count($question->getAnswerValues()) == 2);
    }

    public function testMinimumAndMaximumLengthAndValueAreDetectedCorrectly()
    {
        $question = new Webenq_Model_Question();
        $question->setAnswerValues(array('12', '123456789'));
        $this->assertTrue($question->minLen() == 2);
        $this->assertTrue($question->maxLen() == 9);
        $this->assertTrue($question->minVal() == 12);
        $this->assertTrue($question->maxVal() == 123456789);
    }

    public function testQuestionIsSearchable()
    {
        $result = Webenq_Model_Question::search('e', null, 1);
        $this->assertTrue($result instanceof Doctrine_Collection);
    }

    public function testQuestionIsAutocompletable()
    {
        $result = Webenq_Model_Question::autocomplete('e', null, 1);
        $this->assertTrue(is_array($result));
        if (is_array($result) && key_exists(0, $result)) {
            $this->assertTrue(is_array($result[0]));
            $this->assertTrue(key_exists('value', $result[0]));
            $this->assertTrue(key_exists('label', $result[0]));
        }
    }
}