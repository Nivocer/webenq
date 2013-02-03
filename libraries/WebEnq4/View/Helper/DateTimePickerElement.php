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
 * Helper to generate a date picker and time selection element
 *
 * @package    WebEnq4_Forms
 */
class WebEnq4_View_Helper_DateTimePickerElement
    extends Zend_View_Helper_FormElement
{
    protected $_html = '';

    /**
     * @param string $name
     * @param string $value
     * @param string $attribs
     * @return string Xhtml code for this element
     */
    public function dateTimePickerElement($name, $value = null, $attribs = null)
    {
        $date = new ZendX_JQuery_Form_Element_DatePicker($name);
        $date->setValue($value);
        $date->setJqueryParams(array('dateFormat'=>'yy-m-d', 'showWeek'=>true));
        $this->_html = $date->renderElement();

        return $this->_html;
    }
}
?>