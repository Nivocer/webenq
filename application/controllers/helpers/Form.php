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
 * @package    Webenq_Application
 */
class Webenq_Controller_Action_Helper_Form
    extends Zend_Controller_Action_Helper_Abstract
{
    public function isPostedAndValid(Zend_Form $form)
    {
        $request = $this->getRequest();
        return ($request->isPost() && $form->isValid($request->getPost()));
    }

    public function isCancelled(Zend_Form $form)
    {
        $request = $this->getRequest();
        return ($request->isPost() && $form->isCancelled($request->getPost()));
    }

    public function render(Zend_Form $form)
    {
        $controller = $this->getActionController();

        if (!$form->getAction()) {
            $request = $controller->getRequest();
            $form->setAction($request->getRequestUri());
        }

        $controller->getHelper('viewRenderer')->setNoRender(true);
        $controller->getResponse()->setBody($form->render());
    }
}