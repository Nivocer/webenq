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
 * Downloader class for CSV format
 *
 * This class generates a comma separated values
 * download and sends it to the client.
 *
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Download_Csv extends Webenq_Download
{
    /**
     * Extension of the file for download.
     *
     * @var string
     */
    protected $_extension = 'csv';

    /**
     * The MIME type of the file for download.
     *
     * @var string
     */
    protected $_mimeType = 'text/plain';

    /**
     * String representing the actual document
     *
     * @var string
     */
    protected $_output;

    /**
     * Prepares the document so that it can be send to
     * the client.
     *
     * @return self
     */
    public function init()
    {
        $data = $this->_data;
        $questionnaire = $this->_questionnaire;

        $output = '';
        foreach ($this->_data as $row) {
            foreach ($row as $value) {
                $output .= '"' . $value . '",';

            }
            // remove last comma and add line-end
            $output = substr($output, 0, -1) . "\r\n";
        }

        $this->_output = $output;

        return $this;
    }

    /**
     * Sends the document
     *
     * Sets the headers and body using the provided
     * response object.
     *
     * @param Zend_Controller_Response_Http $response
     */
    public function send(Zend_Controller_Response_Http $response)
    {
        $response
        ->setHeader('Content-Transfer-Encoding', 'binary')
        ->setHeader('Content-Type', $this->getMimeType())
        ->setHeader('Content-Disposition', 'attachment; filename="' . $this->getDownloadFilename() . '"')
        ->setBody($this->_output);
    }
}
