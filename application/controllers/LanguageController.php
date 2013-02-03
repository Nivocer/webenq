<?php
/**
 * WebEnq4
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
 * @package    Webenq_Application
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Controller class
 *
 * @package    Webenq_Application
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class LanguageController extends Zend_Controller_Action
{
    /**
     * Sets the current language and redirects to referer page
     *
     * @return void
     */
    public function selectAction()
    {
        $session = new Zend_Session_Namespace();
        $session->language = $this->_request->language;
        $this->_redirect($_SERVER['HTTP_REFERER']);
    }
}
