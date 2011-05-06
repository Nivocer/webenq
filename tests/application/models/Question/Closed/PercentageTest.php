<?php

class Webenq_Test_Model_Question_Closed_PercentageTest extends Webenq_Test_Model_Question_ClosedTest
{
    /**
     * @dataProvider provideValidData
     */
    public function testFactoryWithValidDataReturnsType($data)
    {
        $question = Webenq_Model_Question::factory($data);
    	$this->assertTrue($question instanceof Webenq_Model_Question_Closed_Percentage);
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testIsTypeFunctionReturnsFalseOnInvalidPercentages($data)
    {
        $question = new Webenq_Model_Question();
        $question->setAnswerValues($data);
    	$this->assertFalse(Webenq_Model_Question_Closed_Percentage::isType($question));
    }
}