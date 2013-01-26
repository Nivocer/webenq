<?php
/**
 * WebEnq4 Library
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
 * @package    WebEnq4_Validate
 * @subpackage Validator
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Checks if a multi-lingual text array has a non-empty text in the default language
 * @category   WebEnq4
 * @package    WebEnq4_Validate
 * @subpackage Validator
 */
class WebEnq4_Validate_MlTextHasDefaultLanguageString extends Zend_Validate_Abstract
{
    const NO_DEFAULT_LANGUAGE = 'noDefaultLanguage';
    const NO_TEXT_IN_DEFAULT_LANGUAGE = 'noTextInDefaultLanguage';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NO_DEFAULT_LANGUAGE => "No default language specified",
        self::NO_TEXT_IN_DEFAULT_LANGUAGE => "No text for default language '%default_language%'",
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'default_language' => '_defaultLanguage'
    );

    /**
     * @var string
     */
    protected $_defaultLanguage = '';

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if $value contains a non-empty string for the default
     * language in $value['default-language']. Empty input is considered valid
     * too.
     *
     * @param  mixed $value Array of '<language>' => '<string>' pairs and
     *         one 'default-language' => '<language>' pair
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_null($value)) {
            if (!is_array($value)) {
                $this->_error(self::NO_DEFAULT_LANGUAGE);
                $this->_error(self::NO_TEXT_IN_DEFAULT_LANGUAGE);
                return false;
            } else {
                if (count($value)>0) {
                    if (!isset($value['default_language'])) {
                        $this->_error(self::NO_DEFAULT_LANGUAGE);
                        return false;
                    } else {
                        $this->_defaultLanguage = $value['default_language'];
                        if (!isset($value[$this->_defaultLanguage])
                        || strlen($value[$this->_defaultLanguage])==0) {
                            $this->_error(self::NO_TEXT_IN_DEFAULT_LANGUAGE);
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }
}

?>