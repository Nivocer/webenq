<?php
class Webenq_Test_Model_Question_Closed_Scale_FourTest extends Webenq_Test_Model_Question_Closed_ScaleTest
{
    /**
     * @dataProvider provideValidData
     */
    public function testQuestionTypeValidatesWithValidAnswerValues($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
        $this->assertTrue(in_array('Webenq_Model_Question_Closed_Scale_Four',
            $question->getValidTypes()));
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testQuestionTypeDoesNotValidateWithInvalidAnswerValues($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertFalse(in_array('Webenq_Model_Question_Closed_Scale_Four',
            $question->getValidTypes()));
    }
}