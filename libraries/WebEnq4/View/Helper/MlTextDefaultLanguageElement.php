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
 * Helper to generate a "multi-lingual text field with choice of default language" element
 *
 * @package    WebEnq4_Forms
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

        if (isset($attribs['languages']) && is_array($attribs['languages'])) {
            $html = '';

            foreach ($attribs['languages'] as $language) {
                $current = '';
                $defaultLanguage = '';

                if (is_array($value)) {
                    $current = (isset($value[$language])) ? $value[$language] : '';
                    $defaultLanguage = (isset($value['default_language'])) ? $value['default_language'] : '';
                }

                $html .= '<span class="languageoption"><span class="selector">';

                $html .= $helperRadio->formRadio(
                    $name . '[default_language]',
                    $defaultLanguage,
                    array(),
                    array($language=>t($language)),
                    ''
                );

                $html .= '</span><span class="inputfield">';

                $html .= $helperText->formText(
                    $name . '[' . $language . ']',
                    $current,
                    array()
                );

                $html .= '</span></span>';
            }

            if ($html != '') {
                $this->_html .= "<span class=\"mltext\">$html</span>";
                $this->_html .= '<span class="hint">&uarr; ' . t("Select the default language") . '</span>';
            }
        }

        return $this->_html;
    }
}