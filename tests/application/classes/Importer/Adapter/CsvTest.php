<?php
class Webenq_Test_Class_Importer_Adapter_CsvTest extends Webenq_Test_Class_Importer_AdapterTest
{
    public function testCanGetDataFromDefaultFile()
    {
        $path = $this->_getPath();
        $adapter = new Webenq_Import_Adapter_Csv("$path/default.csv");
        $data = $adapter->getData();

        $this->assertTrue(is_array($data));
        $this->assertTrue(count($data) === 1);
        $this->assertTrue($data[0][0][0] === 'Emailadres');
    }
}