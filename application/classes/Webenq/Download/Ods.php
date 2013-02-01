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
 * Downloader class for ODS format
 *
 * This class generates an Open Office text document
 * download and sends it to the client.
 *
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Download_Ods extends Webenq_Download
{
    /**
     * Extension of the file for download.
     *
     * @var string
     */
    protected $_extension = 'ods';

    /**
     * The MIME type of the file for download.
     *
     * @var string
     */
    protected $_mimeType = 'application/vnd.oasis.opendocument.spreadsheet';

    /**
     * Filename for temporary storing the document
     *
     * @var string
     */
    protected $_filename;

    /**
     * Prepares the document so that it can be send to
     * the client.
     *
     * @return self
     */
    public function init()
    {
        $attrs = array();

        // convert array to ods-array
        $odsArray = array();
        foreach ($this->_data as $indexRow => $row) {
            foreach ($row as $indexCol => $value) {
                $odsArray[0]['rows'][$indexRow][$indexCol] = array(
                        'attrs' => array(),
                        'value' => str_replace('&#039;', '&apos;', htmlspecialchars($value, ENT_QUOTES)));
            }
        }

        // create ods
        require_once('ods-php/ods.php');
        $ods = new ods();
        $ods->sheets = $odsArray;
        $filename = $this->_filename = sys_get_temp_dir() . '/' . uniqid() . '.ods';
        saveOds($ods, $filename);

        return $this;
    }

    /**
     * Sends the document
     *
     * Sets the headers and body using the provided
     * response object. And removes the temporary file
     * before the response is sent to the client.
     *
     * @param Zend_Controller_Response_Http $response
     */
    public function send(Zend_Controller_Response_Http $response)
    {
        if ($output = file_get_contents($this->_filename)) {
            $response
            ->setHeader('Content-Transfer-Encoding', 'binary')
            ->setHeader('Content-Type', $this->getMimeType())
            ->setHeader('Content-Disposition', 'attachment; filename="' . $this->getDownloadFilename() . '"')
            ->setBody($output);
            @unlink($this->_filename);
        }
    }
}