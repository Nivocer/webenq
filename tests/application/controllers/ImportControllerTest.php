<?php

class HVA_Test_Controller_ImportControllerTest extends HVA_Test_ControllerTest
{
	public function testImportControllerHasSupportedInputFormats()
    {
        $this->dispatch('/import');

        $controller = new ImportController(
            $this->request,
            $this->response,
            $this->request->getParams()
        );
        $controller->indexAction();
        
        $supportedFormats = $controller->getSupportedFormats();
        $this->assertTrue(is_array($supportedFormats));
        $this->assertTrue(count($supportedFormats) > 0);
    }
    
    
    public function testImportOfCsvDatafileIsSuccessful()
    {
    	$this->dispatch('/import');
    	
        $controller = new ImportController(
            $this->request,
            $this->response,
            $this->request->getParams()
        );
        $controller->setDataFile('./testdata/testdata.csv');
        
        try {
            $controller->csvAction();
        }
        catch (Exception $exception) {
            $this->fail('Exception of class "' . get_class($exception) . '" thrown while reading file with test-data.');
        }
    }
}