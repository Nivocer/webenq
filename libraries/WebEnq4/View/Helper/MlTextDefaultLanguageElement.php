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
 * @package    WebEnq4_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/** @see Zend_View_Helper_Abstract */
require_once 'Zend/View/Helper/Abstract.php';

/**
 * Helper to generate a "multi-lingual text field with choice of default language" element
 *
 * @category   WebEnq4
 * @package    WebEnq4_View
 * @subpackage Helper
 */
class WebEnq4_View_Helper_MlTextDefaultLanguageElement
    extends Zend_View_Helper_FormElement
{
    protected $_html = '';

    /**
     * @param string $name Base name to be used for form variables
     * @param string $value May contain default values for the text fields for
     *     each language code, and "$name-default-language" for the default language
     * @param string $attribs Should contain ['languages'] with an array of
     *     language codes to present
     * @return string Xhtml code for this element
     */
    public function mlTextDefaultLanguageElement($name, $value = null, $attribs = null)
    {
        $helperRadio = new Zend_View_Helper_FormRadio();
        $helperRadio->setView($this->view);

        $helperText = new Zend_View_Helper_FormText();
        $helperText->setView($this->view);

        if (isset($attribs['languages']) && is_array($attribs['languages']))
        {
            foreach ($attribs['languages'] as $language)
            {
                $current = '';
                $defaultLanguage = '';

                if (is_array($value))
                {
                    $current = (isset($value[$language])) ? $value[$language] : '';
                    $defaultLanguage = (isset($value['default_language'])) ? $value['default_language'] : '';
                }

                $this->_html .= $helperRadio->formRadio($name . '[default_language]', $defaultLanguage, array(), array($language=>t($language)), '');
                $this->_html .= $helperText->formText($name . '[' . $language . ']', $current, array());
            }
        }

        return $this->_html;
    }
}
?>