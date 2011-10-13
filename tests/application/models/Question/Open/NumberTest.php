<?php
class Webenq_Test_Model_Question_Open_NumberTest extends Webenq_Test_Model_Question_OpenTest
{
    /**
     * @dataProvider provideValidData
     */
    public function testFactoryWithValidDataReturnsType($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertTrue($question instanceof Webenq_Model_Question_Open_Number);
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testFactoryWithInvalidDataDoesNotReturnType($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertFalse($question instanceof Webenq_Model_Question_Open_Number);
    }

    public function testCalculatedSumOfValuesIsCorrect()
    {
        $data = array(1,1,1,1,2,2,2,3,3,4,4,4,5,5,5,5);
    	$question = new Webenq_Model_Question_Open_Number();
    	$question->setAnswerValues($data);

    	$actualResult = $question->sum();
    	$expectedResult = 48;
    	$this->assertTrue($actualResult == $expectedResult);
    }

    public function testCalculatedSumOfStringsIsZero()
    {
        $data = array('test', 'test');
    	$question = new Webenq_Model_Question_Open_Number();
    	$question->setAnswerValues($data);

    	$actualResult = $question->sum();
    	$expectedResult = 0;
    	$this->assertTrue($actualResult == $expectedResult);
    }

    public function testPeriodsAndCommasAreInterpretedCorrectly()
    {
        $validNumbers = array('1.0', '2,1', '2.100', '3,514.00', '+50', -50, 3.5, '-50.578,12');
        $question = Webenq_Model_Question::factory($validNumbers, 'nl');
        $this->assertTrue($question instanceof Webenq_Model_Question_Open_Number);
    }
}