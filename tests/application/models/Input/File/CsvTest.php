<?php

class HVA_Test_Model_Input_File_CsvTest extends HVA_Test_Model_Input_FileTest
{
    /**
     * @expectedException Exception
     */
    public function testInstantiateClassWithInvalidFileThrowsException()
    {
    	Webenq_Import_Adapter_Abstract::factory('thisFileDoesNotExist.csv');
    }
    
    public function testFileHasBeenOpenedForReading()
    {
    	$adapter = Webenq_Import_Adapter_Abstract::factory('testdata/testdata.csv');
    	$this->assertTrue(is_object($adapter));
    }
    
    public function testDataHasBeenReadFromFile()
    {
    	$adapter = Webenq_Import_Adapter_Abstract::factory('testdata/testdata.csv');
    	$data = $adapter->getData();
    	$this->assertTrue(is_array($data));
    	$this->assertTrue(count($data) > 0);
    }
}