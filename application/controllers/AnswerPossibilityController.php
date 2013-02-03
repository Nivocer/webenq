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
class AnswerPossibilityController extends Zend_Controller_Action
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
     * Handles the adding of an answer-possibility
     *
     * @return void
    */
    public function addAction()
    {
        // get group
        $answerPossibilityGroup = Doctrine_Core::getTable('Webenq_Model_AnswerPossibilityGroup')
        ->find($this->_request->id);

        // get form
        $form = new Webenq_Form_AnswerPossibility_Add($answerPossibilityGroup, $this->_helper->language());

        // process posted data
        if ($this->_helper->form->isPostedAndValid($form)) {

            $answerPossibilityText = new Webenq_Model_AnswerPossibilityText();
            $answerPossibilityText->fromArray($form->getValues());

            $answerPossibility = new Webenq_Model_AnswerPossibility();
            $answerPossibility->fromArray($form->getValues());
            $answerPossibility->AnswerPossibilityText[] = $answerPossibilityText;

            try {
                $answerPossibility->save();
                $this->_helper->json(array('reload' => true));
            }
            catch (Exception $e) {
                $form->value->addError($e->getMessage());
            }
        }

        // render view
        $this->_helper->form->render($form);
    }

    public function viewAction()
    {
        // get possibility
        $this->view->answerPossibility = Doctrine_Core::getTable('Webenq_Model_AnswerPossibility')
        ->find($this->_request->id);
    }

    /**
     * Handles the editing of an answer-possibility
     *
     * @return void
     */
    public function editAction()
    {
        // get possibility
        $answerPossibility = Doctrine_Core::getTable('Webenq_Model_AnswerPossibility')
        ->find($this->_request->id);

        // get form
        $form = new Webenq_Form_AnswerPossibility_Edit(
            $answerPossibility, $this->_helper->language()
        );

        // process posted data
        if ($this->_helper->form->isPostedAndValid($form)) {

            $errors = array();

            if (key_exists('submitnull', $this->_request->getPost())) {

                // create new null value
                $nullValue = new Webenq_Model_AnswerPossibilityNullValue();
                $nullValue->value = strtolower($answerPossibility->getAnswerPossibilityText()->text);
                $nullValue->save();

                // remove all answers with this answer possibility
                Doctrine_Query::create()
                ->delete('Webenq_Model_Answer a')
                ->where('a.answerPossibility_id = ?', $answerPossibility->id)
                ->execute();

                // remove answer possibility
                try {
                    $answerPossibility->delete();
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }

            } elseif (key_exists('submitmove', $this->_request->getPost())) {

                // get target answer possibility
                $targetAnswerPossibility = Doctrine_Core::getTable('Webenq_Model_AnswerPossibility')
                ->find($form->synonym->getValue('answerPossibility_id'));

                // make original synonym of target
                $answerPossibilityTextSynonym = new Webenq_Model_AnswerPossibilityTextSynonym();
                $answerPossibilityTextSynonym->text = strtolower($answerPossibility->getAnswerPossibilityText()->text);
                $targetAnswerPossibility->getAnswerPossibilityText()->AnswerPossibilityTextSynonym[] =
                    $answerPossibilityTextSynonym;
                $targetAnswerPossibility->save();

                // update all answers
                Doctrine_Query::create()
                ->update('Webenq_Model_Answer a')
                ->set('a.answerPossibility_id', '?', $targetAnswerPossibility->id)
                ->where('a.answerPossibility_id = ?', $answerPossibility->id)
                ->execute();

                // remove answer possibility
                try {
                    $answerPossibility->delete();
                } catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }

            } elseif (key_exists('submitedit', $this->_request->getPost())) {

                // store possibility
                $answerPossibility->fromArray($form->edit->getValues());
                try {
                    $answerPossibility->save();
                }
                catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }

                // store texts
                $translations = $form->edit->getValues();
                unset($translations['value']);
                unset($translations['answerPossibilityGroup_id']);
                foreach ($translations as $language => $translation) {
                    // ignore empty values
                    if (!$translation) continue;
                    // try to find existing translation
                    $answerPossibilityText = Doctrine_Core::getTable('Webenq_Model_AnswerPossibilityText')
                    ->findOneByAnswerPossibility_idAndLanguage($answerPossibility->id, $language);
                    // or create new one
                    if (!$answerPossibilityText) {
                        $answerPossibilityText = new Webenq_Model_AnswerPossibilityText();
                        $answerPossibilityText->language = $language;
                        $answerPossibilityText->answerPossibility_id = $answerPossibility->id;
                    }
                    // assign translation and save
                    $answerPossibilityText->text = $translation;
                    try {
                        $answerPossibilityText->save();
                    }
                    catch (Exception $e) {
                        $errors[] = $e->getMessage();
                    }
                }
            }

            if (count($errors) > 0) {
                $form->value->addErrors($errors);
            } else {
                $this->_helper->json(array('reload' => true));
            }
        }

        // assign to view
        $this->_helper->form->render($form);
    }

    /**
     * Handles the deleting of an answer-possibility
     *
     * @return void
     */
    public function deleteAction()
    {
        // get answer possibility
        $answerPossibility = Doctrine_Core::getTable('Webenq_Model_AnswerPossibility')
        ->find($this->_request->id);

        $answerPossibilityGroupId = $answerPossibility->answerPossibilityGroup_id;

        /* get form */
        $form = new Webenq_Form_Confirm(
            $answerPossibility->id,
            'Weet u zeker dat u het antwoord "' . $answerPossibility->getAnswerPossibilityText()->text .
            '" wilt verwijderen?'
        );
        $form->setAction($this->_request->getRequestUri());

        /* process posted data */
        if ($this->_request->isPost()) {
            if ($this->_request->yes) {
                try {
                    $answerPossibility->delete();
                } catch(Doctrine_Connection_Mysql_Exception $e) {
                    switch ($e->getCode()) {
                        case 23000:
                            $message = t(
                                'This answer possibility is used in one or more questionnaires and cannot be deleted.'
                            );
                            break;
                        default:
                            $message = $e->getMessage();
                            break;
                    }
                    $this->_helper->viewRenderer->setNoRender();
                    $this->_response->setBody($message);
                    return;
                }
                $this->_helper->json(array('reload' => true));
            }
            $this->_helper->json(array('reload' => false));
        }

        /* render view */
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->form = $form;
        $this->_response->setBody($this->view->render('confirm.phtml'));
    }
}