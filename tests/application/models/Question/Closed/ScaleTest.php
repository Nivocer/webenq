<?php
class Webenq_Test_Model_Question_Closed_ScaleTest extends Webenq_Test_Model_Question_ClosedTest
{
    public function testPercentagesCanNotBeCalculatedByBaseScaleClass()
    {
        if (get_class($this) == 'Webenq_Test_Model_Question_Closed_ScaleTest') {
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
}