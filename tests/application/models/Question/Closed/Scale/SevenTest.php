<?php
class Webenq_Test_Model_Question_Closed_Scale_SevenTest extends Webenq_Test_Case_Model_Question
{
    /**
     * @dataProvider provideValidData
     */
    public function testQuestionTypeValidatesWhenProvidingValidAnswerValues($data)
    {
        $this->loadDatabase();

        $question = Webenq_Model_Question::factory($data, 'nl');
        $this->assertTrue(in_array('Webenq_Model_Question_Closed_Scale_Seven',
                $question->getValidTypes()));
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testFactoryWithInvalidDataDoesNotReturnType($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertFalse($question instanceof Webenq_Model_Question_Closed_Scale_Seven);
    }
}