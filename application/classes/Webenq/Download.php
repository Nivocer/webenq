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
 * @package    Webenq_Data_Export
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Abstract downloader class
 *
 * This class contains the common parts for all
 * downloader classes. The inherited class is
 * responsible for creating the right document type
 * and preparing/sending the response to the client.
 *
 * @package    Webenq_Data_Export
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
abstract class Webenq_Download extends Webenq
{
    /**
     * Questionnaire
     *
     * @var Webenq_Model_Questionnaire $_questionnaire
     */
    protected $_questionnaire;

    /**
     * Array containing the questionnaire data.
     *
     * @var array $_data
     */
    protected $_data = array();

    /**
     * Factory method for easily getting an instance
     * of the right downloader class.
     *
     * @param string $format
     * @param Webenq_Model_Questionnaire $questionnaire
    */
    static public function factory($format, Webenq_Model_Questionnaire $questionnaire = null)
    {
        $class = 'Webenq_Download_' . ucfirst(strtolower($format));
        return new $class($questionnaire);
    }

    /**
     * Class constructor
     *
     * Sets the questionnaire property and calls the
     * init() method that must be implemented by all
     * downloader classes.
     *
     * @param Webenq_Model_Questionnaire $questionnaire
     */
    public function __construct(Webenq_Model_Questionnaire $questionnaire = null)
    {
        if ($questionnaire) {
            $this->_questionnaire = $questionnaire;
            $this->_data = $questionnaire->getDataAsSpreadsheetArray();
            $this->init();
        } else {
            $questionnaire = new Webenq_Model_Questionnaire();
            $this->_questionnaire = $questionnaire;
        }
    }

    /**
     * Manually sets the data
     *
     * This method can be used when a questionnaire has not been provided
     * to the constructor, or when the provided questionnaire was empty
     *
     * @param array Multidimensional array with questionnaire data (only the first sheet)
     * @return $this
     */
    public function setData(array $data)
    {
        if (!empty($this->_data)) {
            throw new Exception('Data was already set!');
        }
        $this->_data = $data;
        return $this;
    }

    /**
     * Prepares the document
     *
     * This method must be implemented by all downloader
     * classes and should prepare the document, so that
     * it is ready to be send to the client.
     */
    abstract function init();

    /**
     * Sends the document
     *
     * This method must be implemented by all downloader
     * classes and should send the document to the client,
     * using the provided response object.
     *
     * @param Zend_Controller_Response_Http $response
    */
    abstract function send(Zend_Controller_Response_Http $response);

    /**
     * Returns the name of the file for download
    */
    public function getDownloadFilename()
    {
        $filename = Webenq::filename(
            implode(
                '-', array(
                $this->_questionnaire->id,
                $this->_questionnaire->getTitle(),
                date('YmdHis')
                )
            )
        ) . '.' . $this->_extension;

        return $filename;
    }

    /**
     * Returns the MIME type of the file for download
     */
    public function getMimeType()
    {
        return $this->_mimeType;
    }
}