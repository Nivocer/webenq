<?php
class Webenq_Test_Model_Question_Open_EmailTest extends Webenq_Test_Model_Question_OpenTest
{
    /**
     * @dataProvider provideValidData
     */
    public function testFactoryWithValidDataReturnsType($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertTrue($question instanceof Webenq_Model_Question_Open_Email);
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testIsTypeFunctionReturnsFalseOnInvalidEmailAddresses($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertFalse($question instanceof Webenq_Model_Question_Open_Email);
    }
}