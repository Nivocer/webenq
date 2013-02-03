<?php
/**
 * Webenq
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
 * @package    Webenq_Tests
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * @package    Webenq_Tests
 */
class Webenq_Test_Model_Input_FileTest extends Webenq_Test_Case_Model
{
    public $setupDatabase = true;

    /**
     * Calls the factory method with an invalid file name. This should
     * result in throwing an Exception.
     *
     * @expectedException Webenq_Import_Adapter_Exception
     */
    public function testFactoryWithInvalidFileThrowsException()
    {
        Webenq_Import_Adapter_Abstract::factory('invalidfile');
    }

    /**
     * Call the factory method with a valid file name. This should return
     * an instance of Webenq_Import_Adapter that can read data from the
     * file.
     */
    public function testFileHasBeenOpenedAndDataHasBeenRead()
    {
        if (isset($this->_validFile)) {
            // get adapter
            $adapter = Webenq_Import_Adapter_Abstract::factory(APPLICATION_PATH .'/../tests/'. $this->_validFile);
            $this->assertTrue(is_object($adapter));

            // get data
            $data = $adapter->getData();
            $this->assertTrue(is_array($data));
            $this->assertTrue(count($data) > 0);
        }
    }
}
