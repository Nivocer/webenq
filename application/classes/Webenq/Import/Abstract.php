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
 * Abstract class containing methods that are shared
 * by all importer classes. All importers must inherit
 * this class.
 *
 * @package    Webenq_Data_Import
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
abstract class Webenq_Import_Abstract implements Webenq_Import_Interface
{
    /**
     * Supported types
     *
     * @var array
     */
    public static $supportedTypes = array(
            'default' => 'default',
            'questback' => 'questback',
    );

    /**
     * Adapter instance
     *
     * @var Webenq_Import_Adapter_Abstract $_adapter
    */
    protected $_adapter;

    /**
     * Import language
     *
     * @var string $_language
     */
    protected $_language;

    /**
     * Constructor
     *
     * @param Webenq_Import_Adapter_Abstract $adapter
     * @param string $language
     */
    public function __construct(Webenq_Import_Adapter_Abstract $adapter, $language)
    {
        $this->_adapter = $adapter;
        $this->_language = $language;
    }

    /**
     * Factors an instance of Webenq_Import_Abstract, depending on the
     * given type.
     *
     * @param string $type
     * @param Webenq_Import_Adapter_Abstract $adapter
     * @param string $language
     * @return Webenq_Import_Abstract
     */
    public static function factory($type, Webenq_Import_Adapter_Abstract $adapter, $language)
    {
        if (in_array($type, self::$supportedTypes)) {
            $class = 'Webenq_Import_' . ucfirst($type);
            return new $class($adapter, $language);
        }

        throw new Exception('Unknown type given in Webenq_Import_Abstract::factory()');
    }

    /**
     * Converts the data retrieved from the adapter to an array with questions
     * as keys and answers as array with values.
     *
     * @param array $data Data retrieved from the adapter
     * @return array Questions as keys and answers as array with values
     */
    public function _getDataAsAnswers($data)
    {
        $return = array();
        $questions = array_shift($data);

        foreach ($questions as $col => $question) {
            //remove whitespace
            $question=trim($question);
            foreach ($data as $row) {
                $return[$question][] = $row[$col];
            }
        }

        return $return;
    }
}