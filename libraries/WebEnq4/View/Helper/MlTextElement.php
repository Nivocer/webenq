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
class WebEnq4_View_Helper_MlTextElement extends Zend_View_Helper_FormElement
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
    public function mlTextElement($name, $value = null, $attribs = null)
    {
        $helperLabel = new Zend_View_Helper_FormLabel();
        $helperLabel->setView($this->view);
        $helperText = new Zend_View_Helper_FormText();
        $helperText->setView($this->view);

        if (is_array($attribs)
        && isset($attribs['languages'])
        && is_array($attribs['languages'])) {
            $html = '';

            $defaultLanguage = (isset($attribs['default_language'])) ? $attribs['default_language'] : '';

            if (isset($attribs['id'])) {
                $id = $attribs['id'];
            }

            foreach ($attribs['languages'] as $language) {
                $current = '';

                if (is_array($value)) {
                    $current = (isset($value[$language])) ? $value[$language] : '';
                }

                $html .= '<span class="languageoption"><span class="selector">';

                $html .= $helperLabel->formLabel(
                    $name,
                    t($language),
                    array()
                );

                $html .= '</span>';

                if ($language == $defaultLanguage) {
                    $html .= '<span class="inputfield default">';
                } else {
                    $html .= '<span class="inputfield">';
                }

                if (isset($id)) {
                    $attribs['id'] = $id . '-' . $language;
                }
                $html .= $helperText->formText(
                    $name . '[' . $language . ']',
                    $current,
                    $attribs
                );

                $html .= '</span></span>';
            }

            if ($html != '') {
                $this->_html = "<span class=\"mltext\">$html</span>";
            }
        }

        return $this->_html;
    }
}