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
abstract class Webenq_Test_Case_Model_Question extends Webenq_Test_Case_Model
{
    public $setupDatabase = true;

    protected function _getPath()
    {
        $dir = str_replace('_', '/', get_class($this));
        if (preg_match('/Webenq\/Test\/Model\//', $dir)) {
            $dir = 'Model/' . str_replace('Webenq/Test/Model/', '', $dir);
            $dir = str_replace('Test', '', $dir);
        }
        return realpath(APPLICATION_PATH . '/../tests/testdata/' . $dir);
    }

    public function provideValidData()
    {
        $testdata = array();

        $dir = $this->_getPath();
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if (is_file($dir . '/' . $file) && substr($file, 0, 5) === "valid") {
                    $contents = file($dir . '/' . $file, FILE_IGNORE_NEW_LINES);
                    $testdata[] = array($contents);
                }
            }
        }

        if (count($testdata) === 0) {
            $testdata[] = array(array());

        }

        return $testdata;
    }

    public function provideInvalidData()
    {
        $testdata = array();

        $dir = $this->_getPath();
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if (is_file($dir . '/' . $file) && substr($file, 0, 7) === "invalid") {
                    $contents = file($dir . '/' . $file, FILE_IGNORE_NEW_LINES);
                    $testdata[] = array($contents);
                }
            }
        }

        if (count($testdata) === 0) {
            $testdata[] = array(array());
        }

        return $testdata;
    }
}
