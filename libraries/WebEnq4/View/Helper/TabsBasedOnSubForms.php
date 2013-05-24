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
class WebEnq4_View_Helper_TabsBasedOnSubForms extends Zend_View_Helper_Abstract
{
    protected $_html = '';
    public function tabsBasedOnSubForms(){
        $this->_html.='<ul>';
        foreach ($this->view->form->getSubforms() as $subForm){
            $subFormName=$subForm->getName();
            $this->_html.=($this->view->activeTab==$subFormName ?
                '<li class="ui-tabs-active">':'<li>');
            $this->_html.= '<a href="#'.$subFormName.'">'. t($subFormName) .'</a>';
            $this->_html.= '</li>';

        }
        $this->_html.='</ul>';
        return $this->_html;
    }
}