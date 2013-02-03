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
 * @package    Webenq
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Controller class
 *
 * @package    Webenq
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class TestController extends Zend_Controller_Action
{
    /**
     * Name of the file to process
     */
    protected $_filename;

    /**
     * Index action
     */
    public function indexAction()
    {
        $form = new Webenq_Form_Test_Index();
        $errors = array();
        $question = null;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {

                $form->file->receive();

                if (!$form->file->isReceived()) {
                    $errors[] = 'Error receiving the file';
                }

                if (!$errors) {
                    try {
                        $filename = $form->file->getFileName();
                        $question = $this->_processTest($filename);
                    } catch (Exception $e) {
                        $errors[] = 'Error processing the file: ' . $e->getMessage();
                    }
                }
            }
        }

        $this->view->form = $form;
        $this->view->errors = $errors;
        $this->view->question = $question;
    }

    /**
     * Processes the testfile
     *
     * @return void
     */
    protected function _processTest($file)
    {
        $f = fopen($file, 'r');

        $data = array();
        while ($line = fgets($f)) {
            $data[] = trim($line);
        }

        return Webenq_Model_Question::factory($data);
    }
}