<?php
class Webenq_Test_Model_Question_Closed_PercentageTest extends Webenq_Test_Case_Model_Question
{
    /**
     * @dataProvider provideValidData
     */
    public function testFactoryWithValidDataReturnsType($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertTrue($question instanceof Webenq_Model_Question_Closed_Percentage);
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testFactoryWithInvalidDataDoesNotReturnType($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
        $this->assertFalse($question instanceof Webenq_Model_Question_Closed_Percentage);
    }
}