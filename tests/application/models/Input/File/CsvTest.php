<?php

class HVA_Test_Model_Input_File_CsvTest extends HVA_Test_Model_Input_FileTest
{
    /**
     * @expectedException Exception
     */
    public function testInstantiateClassWithInvalidFileThrowsException()
    {
    	$csv = new HVA_Model_Input_File_Csv('thisFileDoesNotExist');
    }
    
    public function testFileHasBeenOpenedForReading()
    {
    	$csv = new HVA_Model_Input_File_Csv('testdata/testdata.csv');
    	$this->assertTrue(is_object($csv));
    }
    
    public function testHeadersHaveBeenReadFromFile()
    {
    	$csv = new HVA_Model_Input_File_Csv('testdata/testdata.csv');
    	$headers = $csv->getHeaders();
    	$this->assertTrue(is_array($headers));
    	$this->assertTrue(count($headers) > 0);
    }
}