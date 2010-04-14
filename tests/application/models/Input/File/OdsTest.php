<?php

class HVA_Test_Model_Input_File_OdsTest extends HVA_Test_Model_Input_FileTest
{
    /**
     * @expectedException Exception
     */
    public function testInstantiateClassWithInvalidFileThrowsException()
    {
    	$ods = new HVA_Model_Input_File_Ods('./thisFileDoesNotExist');
    }
    
    public function testFileHasBeenOpenedForReading()
    {
    	$ods = new HVA_Model_Input_File_Ods('testdata/testdata.ods');
    	$this->assertTrue(is_object($ods));
    }
    
    public function testHeadersHaveBeenReadFromFile()
    {
    	$ods = new HVA_Model_Input_File_Ods('testdata/testdata.ods');
    	$headers = $ods->getHeaders();
    	$this->assertTrue(is_array($headers));
    	$this->assertTrue(count($headers) > 0);
    }
}