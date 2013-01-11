<?php
abstract class Webenq_Test_Case_Model_Question extends Webenq_Test_Case_Model
{
    public $setupDatabase = true;

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
