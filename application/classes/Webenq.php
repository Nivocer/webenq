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
 * @package    Webenq
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Class for application wide methods and constants
 *
 * @package		Webenq
 * @author		Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq
{
    const COLLECTION_PRESENTATION_TEXT					    = 'text_only';
    const COLLECTION_PRESENTATION_OPEN_TEXT					= 'open_text';
    const COLLECTION_PRESENTATION_OPEN_TEXTAREA				= 'open_textarea';
    const COLLECTION_PRESENTATION_OPEN_DATE					= 'open_date';
    const COLLECTION_PRESENTATION_OPEN_CURRENTDATE			= 'open_date_current';
    const COLLECTION_PRESENTATION_SINGLESELECT_DROPDOWNLIST	= 'singleselect_dropdownlist';
    const COLLECTION_PRESENTATION_SINGLESELECT_RADIOBUTTONS	= 'singleselect_radiobuttons';
    const COLLECTION_PRESENTATION_SINGLESELECT_SLIDER		= 'singleselect_slider';
    const COLLECTION_PRESENTATION_MULTIPLESELECT_LIST		= 'multipleselect_list';
    const COLLECTION_PRESENTATION_MULTIPLESELECT_CHECKBOXES	= 'multipleselect_checkboxes';
    const COLLECTION_PRESENTATION_RANGESELECT_SLIDER		= 'rangeselect_slider';

    /**
     * Array holding validators
     *
     * The key is the system name used for identifying the validators.
     * The value of each entry is an array holding the keys 'name' (human
     * readable name), 'class' (the class to use) and (optionally) 'options'
     * (an array that holds the parameters to pass to the class' constructor).
     *
     * @var array
     */
     static $_validators = array(
            'not_empty' => array(
                'name' => 'Obliged',
                'class' => 'Zend_Validate_NotEmpty',
            ),
            'range_min' =>array(
                'name'=> 'range lower limit',
                    //@todo find correct validator (alpha  with numbers.s
                'class' => 'Zend_Validate_Float'
             ),
             'range_max' =>array(
                'name'=> 'range upper limit',
                'class' => 'Zend_Validate_Float'
             ),
            'int' => array(
                'name' => 'Numerical (integer, specify range below)',
                'class' => 'Zend_Validate_Int',
            ),
            'reals' => array(
                'name' => 'Numerical (reals, specify range below)',
                'class' => 'Zend_Validate_Float',
            ),
            'alpha_with_whitespace' => array(
                'name' => 'Only alphabetical characters (and whitespaces)',
                'class' => 'Zend_Validate_Alpha',
                'options' => true
            ),
            'alpha_without_whitespace' => array(
                'name' => 'Only alphabetical characters (single word)',
                'class' => 'Zend_Validate_Alpha',
                'options' => false
            ),
            'email_address' => array(
                'name' => 'Email-adres',
                'class' => 'Zend_Validate_EmailAddress',
                ),
            'post_code' => array(
                'name' => 'Postal code',
                'class' => 'Zend_Validate_PostCode',
            ),
    );

    /**
     * Array holding filters
     *
     * The key is the system name used for identifying the filters.
     * The value of each entry is an array holding the keys 'name' (human
     * readable name), 'class' (the class to use) and (optionally) 'options'
     * (an array that holds the parameters to pass to the class' constructor).
     *
     * @var array
    */
    static $_filters = array(
            'string_to_lower' => array(
                    'name' => 'Kleine letters',
                    'class' => 'Zend_Filter_StringToLower',
            ),
            'string_to_upper' => array(
                    'name' => 'Hoofdletters',
                    'class' => 'Zend_Filter_StringToUpper',
            ),
    );

    static public function getValidators()
    {
        $retVal = array();
        foreach (self::$_validators as $key => $validator) {
            if (!in_array($key, array('not_empty', 'range_min', 'range_max')))     {
                $retVal[$key] = $validator['name'];
            }
        }
        return $retVal;
    }

    static public function getFilters()
    {
        $retVal = array();
        foreach (self::$_filters as $key => $filter) {
            $retVal[$key] = $filter['name'];
        }
        return $retVal;
    }

    static public function getValidatorInstance($name)
    {
        if (!isset(self::$_validators[$name])) {
            throw new Exception('Unknown validator:'. $name);
        }

        $validator = self::$_validators[$name];
        if (isset($validator['options'])) {
            $instance = new $validator['class']($validator['options']);
        } else {
            $instance = new $validator['class']();
        }
        return $instance;
    }

    static public function getFilterInstance($name)
    {
        if (!isset(self::$_filters[$name])) {
            throw new Exception('Unknown filter!');
        }

        $filter = self::$_filters[$name];
        if (isset($filter['options'])) {
            $instance = new $filter['class']($filter['options']);
        } else {
            $instance = new $filter['class']();
        }
        return $instance;
    }

    static public function getCollectionPresentationTypes()
    {
        return array(
            'text' => array(
                self::COLLECTION_PRESENTATION_TEXT=>'No answers (text only)'
            ),
            'single' => array(
                self::COLLECTION_PRESENTATION_SINGLESELECT_RADIOBUTTONS => t('One answer from a list (radio)'),
                self::COLLECTION_PRESENTATION_SINGLESELECT_DROPDOWNLIST => t('One answer from a pulldown menu (pulldown)'),
                self::COLLECTION_PRESENTATION_SINGLESELECT_SLIDER => t('One answer using a slider (slider)'),
            ),
            'multiple' => array(
                self::COLLECTION_PRESENTATION_MULTIPLESELECT_CHECKBOXES => t('Multiple answers (checkbox)'),
                self::COLLECTION_PRESENTATION_MULTIPLESELECT_LIST => t('Multiple answers (dropdown (pulldown)'),
            ),
            'open' => array(
                self::COLLECTION_PRESENTATION_OPEN_TEXT => t('Open answer (single lines)'),
                self::COLLECTION_PRESENTATION_OPEN_TEXTAREA => t('Open answer (multiple lines)'),
            ),
            'date' => array(
                self::COLLECTION_PRESENTATION_OPEN_DATE => t('Date picker'),
                self::COLLECTION_PRESENTATION_OPEN_CURRENTDATE => t('Current date'),
            )
         /*   'range select' => array(
                self::COLLECTION_PRESENTATION_RANGESELECT_SLIDER => 'slider',
            ),*/
        );
    }

    static public function getReportPresentationTypes()
    {
        return array(
                'single select' => array(
                        self::COLLECTION_PRESENTATION_SINGLESELECT_DROPDOWNLIST => 'drop-down list',
                        self::COLLECTION_PRESENTATION_SINGLESELECT_RADIOBUTTONS => 'radio buttons',
                ),
                'multiple select' => array(
                        self::COLLECTION_PRESENTATION_MULTIPLESELECT_LIST => 'list',
                        self::COLLECTION_PRESENTATION_MULTIPLESELECT_CHECKBOXES => 'checkboxes',
                ),
        );
    }

    /**
     * Return the given value converted for use with XML. The second parameter
     * indicates the type of xml entity. Valid values are: tag, attribute, value.
     *
     * @param string $value
     * @param string $type
     * @return string
     */
    static public function Xmlify($value, $type = 'value')
    {
        switch ($type) {
            case 'tag':
            case 'attr':
            case 'attribute':
                $value = preg_replace('/[^A-Za-z0-9_]/', '_', $value);
                $value = preg_replace('/_{2,}/', '_', $value);
                $value = preg_replace('/_*$/', null, $value);
                while (preg_match('/^[^A-Za-z]/', $value)) $value = substr($value, 1);
                break;
            case 'val':
            case 'value':
                $value = htmlspecialchars($value);
                break;
            default:
                throw new Exception('No valid type given');
        }
        return $value;
    }

    /**
     * Return the given value converted for use as filename.
     *
     * @param string $value
     * @return string
     */
    static public function filename($value)
    {
        // remove unvalid charachters
        $value = preg_replace('/[^A-Za-z0-9_=\-\+\.]/', null, $value);

        // space to underscore
        $value = preg_replace('/\s+/', '_', $value);

        return $value;
    }

    public static function setMemoryLimit($value)
    {
        $key = 'memory_limit';
        @ini_set($key, $value);
        if (ini_get($key) != $value) {
            throw new Exception("PHP-settings $key could not be set to $value!");
        }
    }

    public static function setMaxExecutionTime($value)
    {
        $key = 'max_execution_time';
        @ini_set($key, $value);
        if (ini_get($key) != $value) {
            throw new Exception("PHP-settings $key could not be set to $value!");
        }
    }
}