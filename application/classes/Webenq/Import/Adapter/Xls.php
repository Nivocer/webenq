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
 * Import adapter for XLS documents
 *
 * @package    Webenq_Data_Import
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Import_Adapter_Xls extends Webenq_Import_Adapter_Abstract
{
    /**
     * Reader class
     */
    protected $_reader = 'Excel5';

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
     * Array with working sheets from data file
     */
    protected $_sheets = array();

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

        /* load library */
        require_once 'PHPExcel/Classes/PHPExcel.php';

        /* open file and read first sheet */
        $reader = PHPExcel_IOFactory::createReader($this->_reader);
        $reader->setReadDataOnly(true);
        $document = $reader->load($filename);
        $this->_sheets = $document->getAllSheets();
    }

    /**
     * Gets the data from the file
     *
     * @return array Nested array of sheets containing questions and answers
     */
    public function getData()
    {
        if ($this->_data) return $this->_data;
        /* iterate over sheets */
        $data = array();
        foreach ($this->_sheets as $sheet) {
            //if many questions, data from questback are on multiple work sheets
            //if so sheet title is questback with a number, we should add this to $data[0]
            $sheetTitle=$sheet->getTitle();
            if ($sheetTitle<>str_replace("QuestBack", "", $sheetTitle)) {
                //sheetTitle is a questback-data sheet, is it the first one
                $questBackSheetNumber=str_replace("QuestBack", "", $sheetTitle);
                if (empty($questBackSheetNumber)) {
                    //we do a array_merge to be sure, the data start in $array[0];
                    $data[] = array_merge($sheet->toArray(), array());
                    $numberOfRowsValid=count($data[0]);
                } else {
                    // add values of the second and further datasheets to $data[0]
                    $dataNextSheet=array_merge($sheet->toArray(), array());

                    foreach ($dataNextSheet as $key=>$valueArray) {
                        foreach ($valueArray as $value) {
                            $data[0][$key][]=$value;
                        }
                    }
                    $headerArray=$dataNextSheet[0];
                    for ($i=$key+1; $i<$numberOfRowsValid;$i++) {
                        //if this sheet has less rows, than the first one, we need to add them
                        //(empty rows for respondents)
                        foreach ($headerArray as $t_value) {
                            $data[0][$i][]=null;
                        }

                    }
                }
            } else {
                //no questback data sheet
                $data[] = array_merge($sheet->toArray(), array());
            }
        }

        // convert date fields
        foreach ($data as $sheetId => $sheet) {
            foreach ($sheet as $rowId => $row) {
                if ($rowId > 0) {
                    foreach ($row as $cellId => $cell) {
                        if (is_float($cell) && $cell >= PHPExcel_Shared_Date::PHPToExcel(0)) {
                            $data[$sheetId][$rowId][$cellId] = gmdate(
                                'Y-m-d H:i:s',
                                PHPExcel_Shared_Date::ExcelToPHP(
                                    $data[$sheetId][$rowId][$cellId]
                                )
                            );
                        }
                    }
                }
            }
        }

        $this->_data = $data;
        return $data;
    }
}
