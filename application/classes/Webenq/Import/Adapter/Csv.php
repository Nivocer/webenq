<?php
/**
 * WebEnq4
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
 * @category   WebEnq4
 * @package    WebEnq4_Data
 * @subpackage Import
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Import adapter for CSV documents
 *
 * @author		Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Import_Adapter_Csv extends Webenq_Import_Adapter_Abstract
{
    /**
     * Array with the data read from the file
     *
     * @var array
     */
    protected $_data;

    /**
     * Filename
     */
    protected $_filename;

    /**
     * File handler
     */
    protected $_fh;

    /**
     * Opens the file for reading
     *
     * @param string $filename
     * @return self
     */
    public function __construct($filename)
    {
        /* check if file exists */
        if (!$filename) {
            throw new Webenq_Import_Adapter_Exception("No data-file was uploaded");
        } elseif (!file_exists($filename)) {
            throw new Webenq_Import_Adapter_Exception("Could not find data-file $filename");
        }

        /* open file and */
        $this->_filename = $filename;
        $this->_fh = fopen($filename, 'r');
    }

    /**
     * Gets the data from the file
     *
     * @return array Nested array of sheets containing questions and answers
     */
    public function getData()
    {
        if ($this->_data) return $this->_data;

        /* iterate over rows */
        $data = array();
        while ($row = fgetcsv($this->_fh)) {
            $data[0][] = $row;
        }

        $this->_data = $data;
        return $data;
    }
}