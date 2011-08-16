<?php
class Webenq_Test_Model_QuestionTest extends Webenq_Test_Case_Model
{
    public function testAnswersCanBeAddedToQuestion()
    {
        $question = new Webenq_Model_Question();
        $question->setAnswerValues(array('answer 1', 'answer 2'));
        $this->assertTrue(count($question->getAnswerValues()) == 2);
    }

    public function testMinimumAndMaximumLengthAndValueAreDetectedCorrectly()
    {
        $question = new Webenq_Model_Question();
        $question->setAnswerValues(array('12', '123456789'));
        $this->assertTrue($question->minLen() == 2);
        $this->assertTrue($question->maxLen() == 9);
        $this->assertTrue($question->minVal() == 12);
        $this->assertTrue($question->maxVal() == 123456789);
    }

    public function testQuestionIsSearchable()
    {
        $result = Webenq_Model_Question::search('e', null, 1);
        $this->assertTrue($result instanceof Doctrine_Collection);
        $this->assertTrue($result->count() === 1);
    }

    public function testQuestionIsAutocompletable()
    {
        $result = Webenq_Model_Question::autocomplete('e', null, 1);
        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 1);
        $this->assertTrue(is_array($result[0]));
        $this->assertTrue(key_exists('value', $result[0]));
        $this->assertTrue(key_exists('label', $result[0]));
    }

    protected function _getPath()
	{
    	$dir = str_replace('_', '/', get_class($this));
    	if (preg_match('/Webenq\/Test\/Model\//', $dir)) {
	    	$dir = 'Model/' . str_replace('Webenq/Test/Model/', '', $dir);
	    	$dir = str_replace('Test', '', $dir);
    	}
    	return realpath(APPLICATION_PATH . '/../tests/testdata/' . $dir);
	}

    public function provideValidData()
    {
    	$testdata = array();

    	$dir = $this->_getPath();
    	if (is_dir($dir)) {
    		$files = scandir($dir);
	    	foreach ($files as $file) {
	    		if (is_file($dir . '/' . $file) && substr($file, 0, 5) === "valid") {
	    			$contents = file($dir . '/' . $file, FILE_IGNORE_NEW_LINES);
	    			$testdata[] = array($contents);
	    		}
	    	}
    	}

    	if (count($testdata) === 0) {
    		$testdata[] = array(array());
    	}

    	return $testdata;
    }

    public function provideInvalidData()
    {
    	$testdata = array();

    	$dir = $this->_getPath();
    	if (is_dir($dir)) {
    		$files = scandir($dir);
	    	foreach ($files as $file) {
	    		if (is_file($dir . '/' . $file) && substr($file, 0, 7) === "invalid") {
	    			$contents = file($dir . '/' . $file, FILE_IGNORE_NEW_LINES);
	    			$testdata[] = array($contents);
	    		}
	    	}
    	}

    	if (count($testdata) === 0) {
    		$testdata[] = array(array());
    	}

    	return $testdata;
    }
}