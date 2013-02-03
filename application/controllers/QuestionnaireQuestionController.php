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
class QuestionnaireQuestionController extends Zend_Controller_Action
{
    /**
     * Controller actions that are ajaxable
     *
     * @var array
     */
    public $ajaxable = array(
        'add' => array('html'),
        'edit' => array('html'),
        'delete' => array('html'),
        'add-subquestion' => array('html'),
    );

    /**
     * Renders the form for adding an existing question to a questionnaire
     */
    public function addAction()
    {
        $questionnaireId = $this->_request->questionnaire_id;
        if (!$questionnaireId) {
            throw new Exception('No questionnaire id given!');
        }
        $form = new Webenq_Form_QuestionnaireQuestion_Add($questionnaireId);
        $form->setAction($this->view->baseUrl('/questionnaire-question/add'));
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                /* store */
                $qq = new Webenq_Model_QuestionnaireQuestion();
                $qq->question_id = str_replace('q_', '', $form->id->getValue());
                $qq->questionnaire_id = $form->questionnaire_id->getValue();
                $qq->CollectionPresentation[0]->type = 'open_text';
                $qq->CollectionPresentation[0]->page = 1;
                $qq->CollectionPresentation[0]->weight = - 1;
                $qq->save();
                /* send response */
                if ($this->_request->isXmlHttpRequest()) {
                    $this->_helper->json(array('reload' => true,));
                }
            }
        }
        $questions = Doctrine_Query::create()
            ->select('q.id, qt.text')
            ->from('Webenq_Model_Question q')
            ->innerJoin('q.QuestionText qt')
            ->where('qt.language = ?', $this->_helper->language())
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        $this->view->form = $form;
        $this->view->questions = $questions;
    }

    /**
     * Renders the form for editing a questionnaire
     *
     * @return void
     */
    public function editAction()
    {
        // get requested questionnaire-question
        $questionnaireQuestion = Doctrine_Core::getTable(
            'Webenq_Model_QuestionnaireQuestion'
        )->find(
            $this->_request->id
        );

        // get form
        $form = new Webenq_Form_QuestionnaireQuestion_Edit($questionnaireQuestion);
        $form->setAction($this->view->baseUrl($this->_request->getPathInfo()));

        // process form
        if ($this->_helper->form->isPostedAndValid($form)) {

            // store the posted values
            $form->storeValues();

            // build redirect url
            $redirectUrl = 'questionnaire/edit/id/' . $questionnaireQuestion->Questionnaire->id;
            if ((int) $questionnaireQuestion->CollectionPresentation[0]->page !== 0) {
                $redirectUrl .= '#page-' . $questionnaireQuestion->CollectionPresentation[0]->page;
            }

            // close dialog and redirect
            if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->json(
                    array(
                        'reload' => true,
                        'href' => $this->view->baseUrl($redirectUrl),
                    )
                );
            } else {
                $this->_redirect($redirectUrl);
            }
        }

        $this->view->form = $form;
        $this->view->questionnaireQuestion = $questionnaireQuestion;
    }

    /**
     * Renders the form for deleting a question from a questionnaire,
     * or completely deleting it from the repository.
     *
     * @return void
     */
    public function deleteAction()
    {
        $questionnaireQuestion = Doctrine_Query::create()
            ->from('Webenq_Model_QuestionnaireQuestion qq')
            ->innerJoin('qq.Question q WITH qq.id = ?', $this->_request->id)
            ->leftJoin('q.QuestionText qt')
            ->where('qt.language = ?', $this->_helper->language())
            ->execute()
            ->getFirst();

        $form = new Webenq_Form_QuestionnaireQuestion_Delete($questionnaireQuestion);
        $form->setAction($this->view->baseUrl($this->_request->getPathInfo()));

        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if (isset($data['yes'])) {
                $questionnaireQuestion->delete();
                if ($this->_request->isXmlHttpRequest()) {
                    $this->_helper->json(array('reload' => true));
                }
            } else {
                if ($this->_request->isXmlHttpRequest()) {
                    $this->_helper->json(array('reload' => false));
                }
            }
        }

        $this->view->form = $form;
        $this->view->questionnaireQuestion = $questionnaireQuestion;
    }

    protected function _getSubQuestions(Webenq_Model_QuestionnaireQuestion $questionnaireQuestion)
    {
        $subQuestions = array();
        foreach ($questionnaireQuestion->CollectionPresentation->getFirst()->CollectionPresentation as $subQuestion) {
            if ($subQuestion->QuestionnaireQuestion->Question->QuestionText->count() > 0) {
                $subQuestions[$subQuestion->weight][0] = $subQuestion->QuestionnaireQuestion->Question->QuestionText[0];
                foreach ($subQuestion->CollectionPresentation as $subSubQuestion) {
                    $subQuestions[$subQuestion->weight][$subSubQuestion->weight] =
                        $subSubQuestion->QuestionnaireQuestion->Question->QuestionText[0];
                }
            }
        }
        /* sort recursively */
        ksort($subQuestions);
        foreach ($subQuestions as $array) {
            ksort($array);
        }
        return $subQuestions;
    }
    /**
     * Saves the current state of the given questionnaire-question
     */
    public function saveStateAction()
    {
        /* disable view/layout rendering */
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout(true);
        $cols = $this->_request->cols;
        $qqIds = (is_array($this->_request->qq)) ? $this->_request->qq : array();
        $parentId = $this->_request->parent;
        /* reproduce grid */
        $rowIndex = 0;
        $grid = array();
        $row = array();
        foreach ($qqIds as $colIndex => $qqId) {
            $rowIndex++;
            $row[] = $qqId;
            if ($rowIndex == $cols || $colIndex == count($qqIds) - 1) {
                $grid[] = $row;
                $row = array();
                $rowIndex = 0;
            }
        }
        $this->_saveGridSubquestions($parentId, $grid);
    }

    /**
     * Stores the grid of subquestions to the database
     *
     * @param int $parentId Parent questionnaire-question
     * @param array $grid Grid with sub-questionnaire-questions
     * @return void
     */
    protected function _saveGridSubquestions($parentId, array $grid)
    {
        /* get collection-presentation object for given parent */
        $cp = Doctrine_Core::getTable(
            'Webenq_Model_QuestionnaireQuestion'
        )->find($parentId)->CollectionPresentation->getFirst();
        /* clear all for this parent */
        Doctrine_Query::create()
            ->update('CollectionPresentation')
            ->set('parent_id', '?', '')
            ->set('weight', '?', '0')
            ->where('parent_id = ?', $cp->id)
            ->execute();
        /* save grid */
        foreach ($grid as $rowIndex => $row) {
            foreach ($row as $colIndex => $col) {
                /* get collection-presentation object for current questionnaire-question */
                $current = Doctrine_Core::getTable('Webenq_Model_QuestionnaireQuestion')
                    ->find($col)
                    ->CollectionPresentation
                    ->getFirst();
                /* clear all for this parent */
                Doctrine_Query::create()
                    ->update('CollectionPresentation')
                    ->set('parent_id', '?', '')
                    ->set('weight', '?', '0')
                    ->where('parent_id = ?', $current->id)
                    ->execute();
                /* save new state */
                if ($colIndex == 0) {
                    /* set parent */
                    $current->parent_id = $cp->id;
                    /* save current as parent for next object */
                    $parent = $current;
                } else {
                    $current->parent_id = $parent->id;
                }
                /* set weight */
                $current->weight = $rowIndex * $rowIndex + $colIndex;
                /* save object */
                $current->save();
            }
        }
    }

    public function addSubquestionAction()
    {
        $qq = Doctrine_Query::create()
            ->from('Webenq_Model_QuestionnaireQuestion qq')
            ->innerJoin('qq.CollectionPresentation cp')
            ->where('qq.id != ?', $this->_request->id)
            ->andWhere('cp.parent_id IS NULL')
            ->execute();
        $this->view->qq = $qq;
    }
}