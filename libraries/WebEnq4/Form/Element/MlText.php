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
 * @package    WebEnq4_Forms
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * @package    WebEnq4_Forms
 */
class WebEnq4_Form_Element_MlText extends Zend_Form_Element_Xhtml
{
    public $helper = 'MlTextElement';

    public function init()
    {
        $this->setAutoInsertNotEmptyValidator(false);
        // @todo The prefix path for extra validators should probably be included elsewhere
        $this->addPrefixPath('WebEnq4_', 'WebEnq4/');
    }

    public function isValid($value)
    {
        $result = true;
        if (($value != null) && ($value != array())) {
            $result = parent::isValid($value);
        }

        if (isset($value['default_language'])){
            $defaultLanguage=$value['default_language'];
        }elseif (isset($this->defaultLanguage)) {
            $defaultLanguage=$this->defaultLanguage;
        }

        //only test when element is required
        if ($result && $this->isRequired()) {
            //do we have a default language from user input or form initiation
            if (!isset($defaultLanguage)){
                $this->addError("no default language set or choosen");
                return false;
            }
            //we need text in default language
            if (!isset($value[$defaultLanguage]) || empty($value[$defaultLanguage])){
                $this->addError(sprintf("you must provide the text in the default language: %s", $defaultLanguage));
                return false;
            }
        }

        return $result;
    }
}