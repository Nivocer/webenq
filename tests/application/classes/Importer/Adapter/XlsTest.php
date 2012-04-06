<?php
class Webenq_Test_Class_Importer_Adapter_XlsTest extends Webenq_Test_Class_Importer_AdapterTest
{
    public function testCanGetDataFromDefaultFile()
    {
        $path = $this->_getPath();
        $adapter = new Webenq_Import_Adapter_Xls("$path/default.xls");
        $data = $adapter->getData();

        $this->assertTrue(is_array($data));
        $this->assertTrue(count($data) === 1);
    }

    public function testCanGetDataFromQuestbackFile()
    {
        $path = $this->_getPath();
        $adapter = new Webenq_Import_Adapter_Xls("$path/questback.xls");
        $data = $adapter->getData();

        $this->assertTrue(is_array($data));
        $this->assertTrue(count($data) === 3);
    }
}