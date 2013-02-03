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
class WebEnq4_Form_Element_MlTextDefaultLanguage extends Zend_Form_Element_Xhtml
{
    public $helper = 'MlTextDefaultLanguageElement';

    public function init()
    {
        $this->setAutoInsertNotEmptyValidator(false);
        $this->addPrefixPath('WebEnq4_', 'WebEnq4/');
        // @todo The prefix path for extra validators should probably be included elsewhere
        $this->addValidator('MlTextHasDefaultLanguageString');
    }

    public function isValid($value)
    {
        $result = true;

        if (($value != null) && ($value != array())) {
            $result = parent::isValid($value);
        }

        // if $value is valid and has a default language set, it has a string
        // in that language too
        if ($result && $this->isRequired()) {
            $result = isset($value['default_language']);
            if (!$result) {
                $this->addError('You must provide the text in at least one language');
            }
        }

        return $result;
    }
}