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
 * @package    Webenq_Questionnaires_Manage
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Controller class
 *
 * @package    Webenq_Questionnaires_Manage
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class AnswerPossibilitySynonymController extends Zend_Controller_Action
{
    public function addAction()
    {
        /* get possibility */
        $answerPossibilityText = Doctrine_Core::getTable('Webenq_Model_AnswerPossibilityText')
            ->find($this->_request->id);

        // get form
        $form = new Webenq_Form_AnswerPossibilitySynonym_Add($answerPossibilityText);

        // process posted data
        if ($this->_helper->form->isPostedAndValid($form)) {

            // store synonym
            $answerPossibilityTextSynonym = new Webenq_Model_AnswerPossibilityTextSynonym();
            $answerPossibilityTextSynonym->fromArray($form->getValues());

            try {
                $answerPossibilityTextSynonym->save();
                $id = $answerPossibilityTextSynonym->AnswerPossibilityText->AnswerPossibility->id;
                $this->_redirect("answer-possibility/view/id/$id");
            }
            catch (Exception $e) {
                   $form->text->addError($e->getMessage());
            }
        }

        /* render view */
        $this->view->form = $form;
        $this->view->answerPossibilityText = $answerPossibilityText;
    }

    public function editAction()
    {
        /* get synonym */
        $synonym = Doctrine_Core::getTable('Webenq_Model_AnswerPossibilityTextSynonym')
            ->find($this->_request->id);

        /* get form */
        $form = new Webenq_Form_AnswerPossibilitySynonym_Edit($synonym);

        /* process posted data */
        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if ($form->isValid($data)) {

                /* store synonym */
                $synonym->fromArray($data);

                try {
                    $synonym->save();
                    $id = $synonym->AnswerPossibilityText->AnswerPossibility->id;
                    $this->_redirect("/answer-possibility/view/id/$id");
                }
                catch (Exception $e) {
                       $form->text->addError($e->getMessage());
                }
            }
        }

        /* assign to view */
        $this->view->form = $form;
        $this->view->synonym = $synonym;
    }

    /**
     * Handles the deleting of an answer-possibility-synonym
     *
     * @return void
     */
    public function deleteAction()
    {
        /* get synonym */
        $synonym = Doctrine_Core::getTable('Webenq_Model_AnswerPossibilityTextSynonym')
            ->find($this->_request->id);

        /* get form */
        $form = new Webenq_Form_Confirm(
            $synonym->id,
            'Weet u zeker dat u het synoniem "' . $synonym->text . '" wilt verwijderen?'
        );

        /* process posted data */
        if ($this->_request->isPost()) {
            if ($this->_request->yes) {
                $synonym->delete();
            }
            $this->_redirect('/answer-possibility/view/id/' . $synonym->AnswerPossibilityText->AnswerPossibility->id);
        }

        /* render view */
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->form = $form;
        $this->_response->setBody($this->view->render('confirm.phtml'));
    }
}