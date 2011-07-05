<?php
class Webenq_Test_Model extends PHPUnit_Framework_TestCase
{
    public function testModelInstanceHasCorrectState()
    {
        $modelClass = get_class($this);
        $modelClass = str_replace('Test_', null, $modelClass);
        $modelClass = str_replace('Test', null, $modelClass);

        if (preg_match('/^Webenq_Model_.*$/', $modelClass)) {
            $model = new $modelClass;
            $this->assertTrue($model->state() === Doctrine_Record::STATE_TCLEAN);
        }
    }
}