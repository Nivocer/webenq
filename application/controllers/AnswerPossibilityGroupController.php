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
class AnswerPossibilityGroupController extends Zend_Controller_Action
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
    );

    /**
     * Renders the overview of question types
     *
     * @return void
    */
    public function indexAction()
    {
        $this->_helper->actionStack('index', 'answer-possibility-null-value');

        /* get answer possibility groups */
        $answerPossibilityGroups = Doctrine_Query::create()
        ->from('Webenq_Model_AnswerPossibilityGroup apg')
        ->orderBy('apg.name')
        ->execute();

        /* render view */
        $this->view->answerPossibilityGroups = $answerPossibilityGroups;
    }

    /**
     * Handles the editing of an answer-possibility-group
     *
     * @return void
     */
    public function viewAction()
    {
        //does group exist
        $groupId=Doctrine_Query::create()
        ->from('Webenq_Model_AnswerPossibilityGroup apg')
        ->where('apg.id = ?', $this->_request->id)
        ->execute();
        $this->view->answerPossibilityGroupId = count($groupId) === 1 ? $groupId->getFirst()->get('id') : false;

        // get group
        $groups = Doctrine_Query::create()
        ->from('Webenq_Model_AnswerPossibilityGroup apg')
        ->innerJoin('apg.AnswerPossibility ap')
        ->innerJoin('ap.AnswerPossibilityText apt')
        ->where('apg.id = ?', $this->_request->id)
        ->orderBy('ap.value, apt.text')
        ->execute();
        $this->view->answerPossibilityGroup = count($groups) === 1 ? $groups->getFirst() : false;
    }

    /**
     * Handles the adding of an answer-possibility-group
     *
     * @return void
     */
    public function addAction()
    {
        // get form
        $form = new Webenq_Form_AnswerPossibilityGroup_Add();

        // process posted data
        if ($this->_helper->form->isPostedAndValid($form)) {
            $answerPossibilityGroup = new Webenq_Model_AnswerPossibilityGroup();
            $answerPossibilityGroup->fromArray($form->getValues());
            $answerPossibilityGroup->save();
            $this->_helper->json(array('reload' => true));
        }

        // render view
        $this->_helper->form->render($form);
    }

    /**
     * Handles the editing of an answer-possibility-group
     *
     * @return void
     */
    public function editAction()
    {
        // get group
        $answerPossibilityGroup = Doctrine_Core::getTable('Webenq_Model_AnswerPossibilityGroup')
        ->find($this->_request->id);

        // get form
        $form = new Webenq_Form_AnswerPossibilityGroup_Edit($answerPossibilityGroup);

        // process posted data
        if ($this->_helper->form->isPostedAndValid($form)) {
            $answerPossibilityGroup->fromArray($form->getValues());
            $answerPossibilityGroup->save();
            $this->_helper->json(array('reload' => true));
        }

        // render view
        $this->_helper->form->render($form);
    }

    /**
     * Handles the deleting of an answer-possibility-group
     *
     * @return void
     */
    public function deleteAction()
    {
        // get group
        $answerPossibilityGroup = Doctrine_Core::getTable('Webenq_Model_AnswerPossibilityGroup')
        ->find($this->_request->id);

        // get form
        $form = new Webenq_Form_Confirm(
            $answerPossibilityGroup->id,
            'Weet u zeker dat u antwoordengroep "' . $answerPossibilityGroup->name . '" wilt verwijderen?'
        );

        /* process posted data */
        if ($this->_helper->form->isPostedAndValid($form)) {
            if ($this->_request->yes) {
                $answerPossibilityGroup->delete();
                $this->_helper->json(array('reload' => true));
            }
            $this->_helper->json(array('reload' => false));
        }

        // render view
        $this->_helper->form->render($form);
    }
}