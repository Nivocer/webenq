<?php
class Webenq_Test_Model_Question_Closed_Scale_TwoTest extends Webenq_Test_Model_Question_Closed_ScaleTest
{
    /**
     * @dataProvider provideValidData
     */
    public function testQuestionTypeValidatesWhenProvidingValidAnswerValues($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
        $this->assertTrue(in_array('Webenq_Model_Question_Closed_Scale_Two',
                $question->getValidTypes()));
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testFactoryWithInvalidDataDoesNotReturnType($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertFalse($question instanceof Webenq_Model_Question_Closed_Scale_Two);
    }
}