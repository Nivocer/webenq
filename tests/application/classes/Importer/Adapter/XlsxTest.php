<?php
class Webenq_Test_Class_Importer_Adapter_XlsxTest extends Webenq_Test_Class_Importer_AdapterTest
{
    public function testCanGetDataFromDefaultFile()
    {
        $path = $this->_getPath();
        $adapter = new Webenq_Import_Adapter_Xlsx("$path/default.xlsx");
        $data = $adapter->getData();

        $this->assertTrue(is_array($data));
        $this->assertTrue(count($data) === 1);
        $this->assertTrue($data[0][0][0] === 'Emailadres');
    }

    public function testCanGetDataFromQuestbackFile()
    {
        $path = $this->_getPath();
        $adapter = new Webenq_Import_Adapter_Xlsx("$path/questback.xlsx");
        $data = $adapter->getData();

        $this->assertTrue(is_array($data));
        $this->assertTrue(count($data) === 3);
        $this->assertTrue($data[0][0][0] === 'Emailadres');
    }
}