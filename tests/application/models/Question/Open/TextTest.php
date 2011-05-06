<?php
class Webenq_Test_Model_Question_Open_TextTest extends Webenq_Test_Model_Question_OpenTest
{
    /**
     * @dataProvider provideValidData
     */
    public function testFactoryWithValidDataReturnsType($data)
    {
        $question = Webenq_Model_Question::factory($data);
    	$this->assertTrue($question instanceof Webenq_Model_Question_Open_Text);
    }
}