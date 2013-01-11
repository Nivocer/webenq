<?php
class Webenq_Test_Model_Question_Open_DateTest extends Webenq_Test_Case_Model_Question
{
    /**
     * @dataProvider provideValidData
     */
    public function testFactoryWithValidDataReturnsType($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertTrue($question instanceof Webenq_Model_Question_Open_Date);
    }

    /**
     * @dataProvider provideInvalidData
     */
    public function testFactoryWithInvalidDataDoesNotReturnType($data)
    {
        $question = Webenq_Model_Question::factory($data, 'nl');
    	$this->assertFalse($question instanceof Webenq_Model_Question_Open_Date);
    }

    public function testDateToTimestampIsConvertedCorrectly()
    {
        $expected = strtotime('1981-02-22');
        $converted = Webenq_Model_Question_Open_Date::toTimestamp('1981-02-22');
        $this->assertTrue($converted == $expected);

        $converted = Webenq_Model_Question_Open_Date::toTimestamp('1981-22-02');
        $this->assertTrue($converted == $expected);

        $converted = Webenq_Model_Question_Open_Date::toTimestamp('02-22-1981');
        $this->assertTrue($converted == $expected);

        $converted = Webenq_Model_Question_Open_Date::toTimestamp('22-02-1981');
        $this->assertTrue($converted == $expected);
    }

    public function testTimestampToDateIsConvertedCorrectly()
    {
        $expected = '1981-02-22';
        $converted = Webenq_Model_Question_Open_Date::toFormat('1981-02-22', 'Y-m-d');
        $this->assertTrue($converted == $expected);

        $converted = Webenq_Model_Question_Open_Date::toFormat('1981-22-02', 'Y-m-d');
        $this->assertTrue($converted == $expected);

        $converted = Webenq_Model_Question_Open_Date::toFormat('02-22-1981', 'Y-m-d');
        $this->assertTrue($converted == $expected);

        $converted = Webenq_Model_Question_Open_Date::toFormat('22-02-1981', 'Y-m-d');
        $this->assertTrue($converted == $expected);
    }

    public function testValidFormatsAreDefined()
    {
        $formats = Webenq_Model_Question_Open_Date::getValidFormats();
        $this->assertTrue(is_array($formats));
        $this->assertTrue(count($formats) > 0);
    }
}