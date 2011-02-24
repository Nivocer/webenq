<?php

class Webenq_Test_Controller_IndexControllerTest extends Webenq_Test_ControllerTest
{
	public function testDummy()
    {
        $this->dispatch('/index');

        $controller = new ImportController(
            $this->request,
            $this->response,
            $this->request->getParams()
        );
        $controller->indexAction();
        
        $this->assertTrue(1 == 1);
    }
}