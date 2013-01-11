<?php
class Webenq_Test_Model_Input_FileTest extends Webenq_Test_Case_Model
{
    public $setupDatabase = true;

    /**
     * Calls the factory method with an invalid file name. This should
     * result in throwing an Exception.
     *
     * @expectedException Webenq_Import_Adapter_Exception
     */
    public function testFactoryWithInvalidFileThrowsException()
    {
        Webenq_Import_Adapter_Abstract::factory('invalidfile');
    }

    /**
     * Call the factory method with a valid file name. This should return
     * an instance of Webenq_Import_Adapter that can read data from the
     * file.
     */
    public function testFileHasBeenOpenedAndDataHasBeenRead()
    {
        if (isset($this->_validFile)) {
            // get adapter
            $adapter = Webenq_Import_Adapter_Abstract::factory(APPLICATION_PATH .'/../tests/'. $this->_validFile);
            $this->assertTrue(is_object($adapter));

            // get data
            $data = $adapter->getData();
            $this->assertTrue(is_array($data));
            $this->assertTrue(count($data) > 0);
        }
    }
}
