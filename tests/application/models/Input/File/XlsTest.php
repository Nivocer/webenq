<?php

class Webenq_Test_Model_Input_File_XlsTest extends Webenq_Test_Model_Input_FileTest
{
    /**
     * @expectedException Exception
     */
    public function testInstantiateClassWithInvalidFileThrowsException()
    {
    	Webenq_Import_Adapter_Abstract::factory('./thisFileDoesNotExist.xls');
    }
    
    public function testFileHasBeenOpenedForReading()
    {
    	$adapter = Webenq_Import_Adapter_Abstract::factory('testdata/testdata.xls');
    	$this->assertTrue(is_object($adapter));
    }
    
    public function testDataHasBeenReadFromFile()
    {
    	$adapter = Webenq_Import_Adapter_Abstract::factory('testdata/testdata.xls');
    	$data = $adapter->getData();
    	$this->assertTrue(is_array($data));
    	$this->assertTrue(count($data) > 0);
    }
}