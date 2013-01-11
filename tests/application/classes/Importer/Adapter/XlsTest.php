<?php
/**
 * WebEnq4 Library
 *
 *  LICENSE
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category   Webenq
 * @package    Webenq_Classes
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

class Webenq_Test_Class_Importer_Adapter_XlsTest extends Webenq_Test_Case_Class
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