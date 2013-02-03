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
 * @package    Webenq_Output
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * @package    Webenq_Output
 */
abstract class Webenq_Print extends Webenq
{
    protected $_questionnaire = array();

    /**
     * Factory method for easily getting an instance
     * of the right printer class.
     *
     * @param string $format
     * @param array|Questionnaire $questionnaire
    */
    static public function factory($format, $questionnaire)
    {
        if (is_array($questionnaire)) {
            $array = $questionnaire;
            $questionnaire = new Webenq_Model_Questionnaire();
            $questionnaire->fromArray($array);
        }

        $class = 'Webenq_Print_' . ucfirst(strtolower($format));
        return new $class($questionnaire);
    }

    public function __construct(Questionnaire $questionnaire)
    {
        $this->_questionnaire = $questionnaire;
        $this->init();
    }

    abstract function init();

    abstract function send(Zend_Controller_Response_Http $response);

    public function getDownloadFilename()
    {
        return $this->_downloadFilename;
    }

    public function getMimeType()
    {
        return $this->_mimeType;
    }
}