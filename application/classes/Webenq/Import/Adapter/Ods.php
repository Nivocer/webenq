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
 * @package    Webenq_Data_Import
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Import adapter for ODS documents
 *
 * @package    Webenq_Data_Import
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Import_Adapter_Ods extends Webenq_Import_Adapter_Abstract
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
     * Object representing the input file
     */
    protected $_f;

    /**
     * Array with tables from data file
     */
    protected $_tables = array();

    /**
     * Array with the data read from the file
     *
     * @var array
    */
    protected $_rawData;

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

        $this->_filename = $filename;

        /* load ODS file */
        require_once 'odtphp/odtphp/Nivocer.php';
        @$this->_f = new OdtPhp_Nivocer($filename);

        /* read tables from file */
        $this->_tables = $this->_getTables();
    }

    /**
     * Finds the child node with the given name
     *
     * @param DOMNode $node The parent node to search
     * @param string $name The name of the child node
     * @return array of DOMNode objects The found child nodes, or empty array
     */
    protected function _findChildNode(DOMNode $node, $name)
    {
        $elements = array();
        foreach ($node->childNodes as $child) {
            if ($child->nodeName === $name) {
                $elements[] = $child;
            }
        }
        return $elements;
    }

    /**
     * Gets the relevant part of the XML (the actual table with data)
     *
     * @return DOMNode The node containing the data table
     */
    protected function _getTables()
    {
        $document = new DOMDocument;
        $document->loadXML($this->_f->getContentXml());

        /* office:document-content */
        $content = $document->childNodes->item(0);

        /* office:body */
        $elms = $this->_findChildNode($content, 'office:body');
        $body = $elms[0];

        /* office:spreadsheet */
        $elms = $this->_findChildNode($body, 'office:spreadsheet');
        $spreadsheet = $elms[0];

        /* table:table */
        $tables = $this->_findChildNode($spreadsheet, 'table:table');

        return $tables;
    }

    /**
     * Checks if the given array contains values
     *
     * @param array $array The array to check
     * @return bool True if it contains values, false otherwise
     */
    protected function _hasValues(array $array)
    {
        foreach ($array as $value) {
            if ($value) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets the data from the file
     *
     * @return array Nested array of sheets containing questions and answers
     */
    public function getData()
    {
        if ($this->_data) return $this->_data;

        $data = array();

        /* iterate over sheets */
        foreach ($this->_tables as $sheetIndex => $sheet) {

            /* iterate over rows */
            $rowIndex = 0;
            foreach ($sheet->childNodes as $row) {

                /* continue if it isn't a row */
                if ($row->nodeName !== 'table:table-row') continue;

                /* iterate over columns */
                foreach ($row->childNodes as $cell) {

                    /* continue if it isn't a cell */
                    if (!$cell instanceof DOMNode) continue;

                    /* check for end */
                    if ($rowIndex == 0 && !$cell->nodeValue) break;

                    /* put current cell's value in data array */
                    $data[$sheetIndex][$rowIndex][] = $cell->nodeValue;

                    /* check for repeating values */
                    if ($cell->hasAttributes() && $cell->attributes->getNamedItem('number-columns-repeated')) {
                        $repeat = $cell->attributes->getNamedItem('number-columns-repeated')->value;
                        for ($r = 1; $r < $repeat; $r++) {
                            $data[$sheetIndex][$rowIndex][] = $cell->nodeValue;
                        }
                    }

                    /* remove empty cells at the end */
                    if ($rowIndex > 0) {
                        while (count($data[$sheetIndex][$rowIndex]) > count($data[$sheetIndex][0])) {
                            array_pop($data[$sheetIndex][$rowIndex]);
                        }
                    }
                }
                $rowIndex++;
            }
        }

        $this->_data = $data;
        return $data;
    }
}