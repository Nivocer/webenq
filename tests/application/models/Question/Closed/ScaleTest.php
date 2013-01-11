<?php
class Webenq_Test_Model_Question_Closed_ScaleTest extends Webenq_Test_Case_Model_Question
{
    public function testPercentagesCanNotBeCalculatedByBaseScaleClass()
    {
        try {
            $data = array(1,1,1,1,2,2,2,3,3,4,4,4,5,5,5,5);
        	$question = new Webenq_Model_Question_Closed_Scale();
        	$question->setAnswerValues($data);
        	$question->getPercentages();
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Exception);
        }
    }
}