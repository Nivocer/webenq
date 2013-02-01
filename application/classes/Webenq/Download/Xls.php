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
 * @subpackage Export
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Downloader class for XLS format
 *
 * This class generates a Microsoft Excel download
 * and sends it to the client.
 *
 * @author Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Download_Xls extends Webenq_Download
{
    /**
     * Extension of the file for download.
     *
     * @var string
     */
    protected $_extension = 'xls';

    /**
     * The MIME type of the file for download.
     *
     * @var string
     */
    protected $_mimeType = 'application/vnd.ms-excel';

    /**
     * An object representing the actual document
     *
     * @var PHPExcel
     */
    protected $_document;

    /**
     * Prepares the document so that it can be send to
     * the client.
     *
     * @return self
     */
    public function init()
    {
        // load library and create document
        require_once 'PHPExcel/Classes/PHPExcel.php';
        $xls = new PHPExcel();
        $sheet = $xls->getActiveSheet();

        // add data from array to xls
        foreach ($this->_data as $indexRow => $row) {
            foreach ($row as $indexCol => $value) {
                $sheet->setCellValueByColumnAndRow($indexCol, $indexRow+1, $value);
            }
        }

        $this->_document = $xls;

        return $this;
    }

    public function addWorkingSheet(array $data)
    {
        $sheet = $this->_document->createSheet();

        // add data from array to xls
        foreach ($data as $indexRow => $row) {
            foreach ($row as $indexCol => $value) {
                $sheet->setCellValueByColumnAndRow($indexCol, $indexRow+1, $value);
            }
        }
    }

    /**
     * Sends the document
     *
     * Sets and sends the headers using the provided
     * response object. And then writes the document
     * to the standard output.
     *
     * @param Zend_Controller_Response_Http $response
     */
    public function send(Zend_Controller_Response_Http $response)
    {
        $response
        ->setHeader('Content-Transfer-Encoding', 'binary')
        ->setHeader('Content-Type', $this->getMimeType())
        ->setHeader('Content-Disposition', 'attachment; filename="' . $this->getDownloadFilename() . '"')
        ->sendHeaders();

        $writer = $this->_getWriter();
        $writer->save('php://output');
    }



    public function save($fileNameBase)
    {
        $writer = $this->_getWriter();
        $fileName='outputs/'.$fileNameBase.$this->getDownloadFilename();
        $writer->save($fileName);
        return $fileName;
    }


    /**
     * Returns a writer instance for the given document
     * type.
     *
     * @return PHPExcel_Writer_IWriter
     */
    protected function _getWriter()
    {
        return PHPExcel_IOFactory::createWriter($this->_document, 'Excel5');
    }
}
