<?php
class Webenq_Test_Model_Question_Closed_Scale_FiveTest extends Webenq_Test_Case_Model_Question
{
    /**
     * @dataProvider provideValidData
     */
    public function testFactoryWithValidDataReturnsType($data)
    {
        $this->loadDatabase();

        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertTrue($question instanceof Webenq_Model_Question_Closed_Scale_Five);
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testFactoryWithInvalidDataDoesNotReturnType($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertFalse($question instanceof Webenq_Model_Question_Closed_Scale_Five);
    }

    public function testCalculatedPercentagesAreCorrect()
    {
        $data = array(1,1,1,1,2,2,2,3,3,4,4,4,5,5,5,5);
    	$question = new Webenq_Model_Question_Closed_Scale_Five();
    	$question->setAnswerValues($data);

    	$actualResult = $question->getPercentages();
    	$expectedResult = array(1 => 25, 2 => 18.75, 3 => 12.5, 4 => 18.75, 5 => 25);
    	$this->assertTrue($actualResult == $expectedResult);
    	$this->assertTrue(array_sum($actualResult) == 100);

    	$actualResult = $question->getNegativeNeutralPositivePercentages();
    	$expectedResult = array(0 => 43.75, 1 => 12.5, 2 => 43.75);
    	$this->assertTrue($actualResult == $expectedResult);
    	$this->assertTrue(array_sum($actualResult) == 100);
    }
}