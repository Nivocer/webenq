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
 * @package    Webenq_Data_Import
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Controller class
 *
 * @package    Webenq_Data_Import
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class ImportController extends Zend_Controller_Action
{
    /**
     * Handles the importing of files
     *
     * @return void
     */
    public function indexAction()
    {
        $session = new Zend_Session_Namespace();
        $supportedFormats = Webenq_Import_Adapter_Abstract::$supportedFormats;

        $form = new Webenq_Form_Import($supportedFormats);
        $form->language->setValue($session->language);
        $errors = array();

        if ($this->_helper->form->isPostedAndValid($form)) {

            // make sure enough resources are assigned
            try {
                //Webenq::setMemoryLimit('512M');
                Webenq::setMaxExecutionTime(0);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }

            // get form data
            $data = $form->getValues();
            $session->language = $data['language'];

            // get uploaded file
            if ($form->file->receive()) {
                $filename = $form->file->getFileName();
            } else {
                $errors[] = 'Error receiving the file';
            }

            if (empty($errors)) {
                $adapter = Webenq_Import_Adapter_Abstract::factory($filename);
                $importer = Webenq_Import_Abstract::factory($data['type'], $adapter, $data['language']);
                $importer->import();
                $this->_redirect('/');
            }
        }

        $this->view->errors = $errors;
        $this->view->form = $form;
    }
}
