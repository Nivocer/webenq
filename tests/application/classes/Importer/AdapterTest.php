<?php
class Webenq_Test_Class_Importer_AdapterTest extends Webenq_Test_Class_ImporterTest
{
    public function test()
    {

    }

    protected function _getPath()
    {
        $dir = str_replace('_', '/', get_class($this));
        if (preg_match('/Webenq\/Test\/Class\//', $dir)) {
            $dir = 'Class/' . str_replace('Webenq/Test/Class/', '', $dir);
            $dir = str_replace('Test', '', $dir);
        }
        return realpath(APPLICATION_PATH . '/../tests/testdata/' . $dir);
    }
}