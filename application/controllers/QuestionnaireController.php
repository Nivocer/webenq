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
class QuestionnaireController extends Zend_Controller_Action
{
    /**
     * Controller actions that are ajaxable
     *
     * @var array
     */
    public $ajaxable = array(
        //'add' => array('html'),
    );

    /**
     * Renders the overview of questionnaires, and a form to add a new questionnaire.
     *
     * @return void
    */
    public function indexAction()
    {
        if (!isset($this->view->confirmForm)
        && !isset($this->view->propertiesForm)) {
            $form = new Webenq_Form_Questionnaire_Properties();
            $form->setAction($this->_request->getBaseUrl() . '/questionnaire/add');
            $form->setAttrib('class', 'hidden');
            $this->view->propertiesForm = $form;
        }

        $this->view->questionnaires = Webenq_Model_Questionnaire::getQuestionnaires();
    }

    /**
     * Renders an xform
     *
     * @return void
     */
    public function xformAction()
    {
        $questionnaire = Webenq_Model_Questionnaire::getQuestionnaire($this->_request->id, $this->_helper->language());

        $filename = Webenq::filename(
            implode(
                '-', array(
                    'xform-def',
                    $questionnaire->id,
                    $questionnaire->getQuestionnaireTitle()->text,
                    date('YmdHis')
                )
            ) . '.xml'
        );

        $xml = $questionnaire->getXform();

        //check if layout helper is definied before disabling layout (provides errors for phpunit)
        if ($this->_helper->hasHelper('layout')) {
            $this->_helper->layout->disableLayout();
        }
        $this->_helper->viewRenderer->setNoRender();
        $this->_response
        ->setHeader('Content-Type', 'text/xml; charset=utf-8')
        ->setHeader('Content-Transfer-Encoding', 'binary')
        ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
        ->setBody($xml->saveXML());
    }

    /**
     * Renders xform data
     *
     * @return void
     */
    public function xformDataAction()
    {
        $questionnaire = Webenq_Model_Questionnaire::getQuestionnaire($this->_request->id, $this->_helper->language());

        $filename = Webenq::filename(
            implode(
                '-', array(
                    'xform-data',
                    $questionnaire->id,
                    $questionnaire->getQuestionnaireTitle()->text,
                    date('YmdHis')
                )
            )
        ) . '.xml';

        $xml = $questionnaire->getXformData();
        //save file in case of large files and connection/keep-alive time-out
        $tempPath=ini_get('upload_tmp_dir');
        if (empty($tempPath)) {
            $tempPath='/tmp';
        }
        $tempFile=$tempPath.'/'.$filename;
        $xml->save($tempFile);

        //check if layout helper is definied before disabling layout (provides errors for phpunit)
        if ($this->_helper->hasHelper('layout')) {
            $this->_helper->layout->disableLayout();
        }
        $this->_helper->viewRenderer->setNoRender();
        $this->_response
        ->setHeader('Content-Type', 'text/xml; charset=utf-8')
        ->setHeader('Content-Transfer-Encoding', 'binary')
        ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $this->getResponse()->sendHeaders();
        //reading file to setbody(readfile($fileTemp), returns also de size of the file in the xml file.
        //@todo check to see if there is a better solution
        readfile($tempFile);
    }

    /**
     * Add a questionnaire: process the properties form, then continue to
     * the questionnaires index.
     *
     * @return void
     * @todo Add Questionnaire Properties Controller Tests
     */
    public function addAction()
    {
        $form = new Webenq_Form_Questionnaire_Properties();
        $form->setAction($this->_request->getRequestUri());

        if ($this->_helper->form->isPostedAndValid($form)) {
            if (!$this->_helper->form->isCancelled($form)) {
                $questionnaire = new Webenq_Model_Questionnaire();
                $formValues = $form->getValues();
                if (isset($formValues['id'])) {
                    unset($formValues['id']);
                }
                $questionnaire->fromArray($formValues);
                // @todo remove this from here, perhaps into model (if needed)
                $questionnaire->meta = serialize(array('timestamp' => time()));
                $questionnaire->save();
                $this->_helper->FlashMessenger()
                    ->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            t('Questionnaire "%s" added succesfully'),
                            $questionnaire->getQuestionnaireTitle()->text
                        )
                    );
            }
            // redirect via URL to prevent re-posting
            $this->_redirect('questionnaire');
        }

        $this->view->propertiesForm = $form;
        $this->_forward('index');
    }

    /**
     * Edit a questionnaire: process the properties form, and show the questions
     * in this questionnaire (with its own edit features).
     *
     * @return void
     * @todo Add Questionnaire Properties Controller Tests
     */
    public function editAction()
    {
        $questionnaire = Webenq_Model_Questionnaire::getQuestionnaire(
            $this->_request->id, $this->_helper->language()
        );
        if (!$questionnaire) $this->_redirect('questionnaire');

        $form = new Webenq_Form_Questionnaire_Properties();
        $form->setAction($this->_request->getRequestUri());

        if ($this->_helper->form->isPostedAndValid($form)) {
            if (!$this->_helper->form->isCancelled($form)) {
                $formValues = $form->getValues();
                if (isset($formValues['id'])
                && $formValues['id']==$questionnaire->get('id')) {
                    $questionnaire->fromArray($formValues);
                    // @todo remove this from here, perhaps into model (if needed)
                    $questionnaire->meta = serialize(array('timestamp' => time()));
                    $questionnaire->save();
                    $this->_helper->FlashMessenger()
                    ->setNamespace('success')
                    ->addMessage(
                        sprintf(
                            t('Questionnaire "%s" updated succesfully'),
                            $questionnaire->getQuestionnaireTitle()->text
                        )
                    );
                } else {
                    $this->_helper->FlashMessenger()
                    ->setNamespace('error')
                    ->addMessage(
                        t('Questionnaire identifier mismatch, something went wrong')
                    );
                }
            }

            // redirect via URL to prevent re-posting
            $this->_redirect($this->_request->getPathInfo());
        }

        $form->setDefaults($questionnaire->toArray());
        $form->setAttrib('class', 'hidden');

        $this->view->propertiesForm = $form;
        $this->view->questionnaire = $questionnaire;
        $this->view->totalPages = Webenq_Model_Questionnaire::getTotalPages($questionnaire['id']);
    }

    public function orderAction()
    {
        /* disable view/layout rendering */
        $this->_helper->viewRenderer->setNoRender(true);
        //check if layout helper is definied before disabling layout (provides errors for phpunit)
        if ($this->_helper->hasHelper('layout')) {
            $this->_helper->layout->disableLayout(); // disable layouts
        }

        if ($this->_request->data) {
            $this->_orderPagesAndQuestions(Zend_Json::decode($this->_request->data));
        }elseif ($this->_request->questionnaire){
            $this->_orderQuestionnaires(Zend_Json::decode($this->_request->questionnaire));
        } elseif ($this->_request->question) {
            $this->_orderSubQuestions(Zend_Json::decode($this->_request->question));
        }
    }

    protected function _orderPagesAndQuestions(array $data)
    {
        if (count($data) === 0) return;

        foreach ($data as $key => $val) {

            $page = $key + 1;

            $qqIds = array();
            foreach ($val as $id) {
                $id = (int) str_replace('qq_', null, $id);
                $qqIds[] = $id;
            }

            if (empty($qqIds)) continue;

            // reset all questions on this page
            Doctrine_Query::create()
            ->update('Webenq_Model_CollectionPresentation cp')
            ->set('weight', '?', 0)
            ->set('page', '?', $page)
            ->whereIn('cp.questionnaire_question_id', $qqIds)
            ->execute();

            // get questions on this page
            $qqs = Doctrine_Query::create()
            ->from('Webenq_Model_QuestionnaireQuestion qq')
            ->leftJoin('qq.CollectionPresentation cp')
            ->whereIn('qq.id', $qqIds)
            ->execute();

            // set new weight
            foreach ($qqs as $weight => $qq) {

                // set new weight
                $qq->CollectionPresentation[0]->weight = array_search($qq->id, $qqIds);
                $qq->save();

                // make sure the page is also set on sub-questions
                Doctrine_Query::create()
                ->update('Webenq_Model_CollectionPresentation cp')
                ->set('page', '?', $page)
                ->where('cp.parent_id = ?', $qq->CollectionPresentation[0]->id)
                ->execute();
            }
        }
    }
/**
 * save new sort order of questionnaire, triggered by javascript sortable action
 *
 * @param array $data
 */
    protected function _orderQuestionnaires(array $data)
    {
        if (count($data) === 0) {
            return;
        }

        $qIds = array();
        foreach ($data as $key => $id) {
            $id = (int) str_replace('q_', null, $id);
            $qIds[] = $id;
        }

        // reset questionnaire
        Doctrine_Query::create()
        ->update('Webenq_Model_Questionnaire q')
        ->set('weight', '?', 1)
        ->whereIn('q.id', $qIds)
        ->execute();

        // get questions
        $qs = Doctrine_Query::create()
        ->from('Webenq_Model_Questionnaire q')
        ->whereIn('q.id', $qIds)
        ->execute();

        // set new weight
        foreach ($qs as $weight => $q) {
            $q->weight = array_search($q->id, $qIds);
            $q->save();
        }
    }

    protected function _orderSubQuestions(array $data)
    {
        if (count($data) === 0) {
            return;
        }

        $qqIds = array();
        foreach ($data as $key => $id) {
            $id = (int) str_replace('qq_', null, $id);
            $qqIds[] = $id;
        }

        // reset subquestions
        Doctrine_Query::create()
        ->update('Webenq_Model_CollectionPresentation cp')
        ->set('weight', '?', 0)
        ->set('page', '?', 0)
        ->whereIn('cp.questionnaire_question_id', $qqIds)
        ->execute();

        // get subquestions
        $qqs = Doctrine_Query::create()
        ->from('Webenq_Model_QuestionnaireQuestion qq')
        ->leftJoin('qq.CollectionPresentation cp')
        ->whereIn('qq.id', $qqIds)
        ->execute();

        // set new weight
        foreach ($qqs as $weight => $qq) {
            $qq->CollectionPresentation[0]->weight = array_search($qq->id, $qqIds);
            $qq->save();
        }
    }

    public function addQuestionAction()
    {
        $qq = new Webenq_Model_QuestionnaireQuestion();
        $qq->questionnaire_id = $this->_request->questionnaire_id;
        $qq->question_id = $this->_request->question_id;
        $cp = new Webenq_Model_CollectionPresentation();
        $cp->weight = -1;
        $qq->CollectionPresentation[] = $cp;
        $qq->ReportPresentation[] = new Webenq_Model_ReportPresentation();
        $qq->save();

        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout(true);
        $this->_response->setBody($qq->id);
    }

    /**
     * Delete a questionnaire: process a confirmation form, then continue to
     * the questionnaires index.
     *
     * @return void
     */
    public function deleteAction()
    {
        $questionnaire = Webenq_Model_Questionnaire::getQuestionnaire(
            $this->_request->id, $this->_helper->language()
        );
        if (!$questionnaire) $this->_redirect('questionnaire');

        $title = $questionnaire->getQuestionnaireTitle()->text;

        $form = new Webenq_Form_Confirm();
        $form->setConfirmation(
            $questionnaire->id,
            sprintf(
                t('Are you sure you want to delete questionnaire "%s"
                    (including all questions and answers)?'),
                $title
            )
        );

        if ($this->_helper->form->isPostedAndValid($form)) {
            if (!$this->_helper->form->isCancelled($form)) {
                $formValues = $form->getValues();
                if (isset($formValues['id'])
                && $formValues['id']==$questionnaire->get('id')) {
                    $questionnaire->delete();
                    $this->_helper->FlashMessenger()
                    ->setNamespace('success')
                    ->addMessage(
                        sprintf(t('Questionnaire "%s" deleted'), $title)
                    );
                } else {
                    $this->_helper->FlashMessenger()
                    ->setNamespace('error')
                    ->addMessage(
                        t('Questionnaire identifier mismatch, something went wrong')
                    );
                }
            }

            // redirect via URL to prevent re-posting
            $this->_redirect('/questionnaire');
        }

        $this->view->confirmForm = $form;
        $this->_forward('index');
    }

    /**
     * Renders the data collection for the given questionnaire
     *
     * @return void
     */
    public function collectAction()
    {
        // get session
        $session = new Zend_Session_Namespace();

        // reset respondent in session if coming from another questionnaire
        if ((int) $session->questionnaire_id !== (int) $this->_request->id) {
            $session->respondent_id = null;
        }

        // get or create respondent
        if ($this->_request->respondent_id) {
            $respondent = Doctrine_Core::getTable('Webenq_Model_Respondent')
            ->find($this->_request->respondent_id);
        } else if ($session->respondent_id) {
            $respondent = Doctrine_Core::getTable('Webenq_Model_Respondent')
            ->find($session->respondent_id);
        } else {
            $respondent = new Webenq_Model_Respondent();
            $respondent->questionnaire_id = $this->_request->id;
            $respondent->save();
        }

        // get current page
        $page = isset($this->_request->page) ? (int) $this->_request->page : 1;

        // store respondent's id and questionnaire's id to session
        $session->respondent_id = $respondent->id;
        $session->questionnaire_id = $this->_request->id;

        // get questions for current page
        $questionnaire = Webenq_Model_Questionnaire::getQuestionnaire(
            $this->_request->id,
            $this->_helper->language(),
            $page,
            $respondent,
            true
        );
        if ($questionnaire) {
            $qqs = $questionnaire->QuestionnaireQuestion;
        } else {
            // redirect if no more questions
            $this->_redirect('questionnaire');
        }

        // get and populate form
        $form = new Webenq_Form_Questionnaire_Collect($qqs, $respondent);

        // get progress data
        $totalQuestions = $questionnaire->getTotalQuestions();
        $answeredQuestions = $questionnaire->countAnsweredQuestions($respondent);

        if ($this->_helper->form->isPostedAndValid($form)) {
            // process posted data
            foreach ($form->getValues() as $key => $value) {
                $this->_processPostedValue($key, $value, $respondent);
            }
            // increase page number and reload page
            $page++;
            $this->_redirect("questionnaire/collect/id/$questionnaire->id/page/$page");
        }

        // display form
        $this->view->form = $form;
        $this->view->pageNr = $page;
        $this->view->progress = array(
                'total' => $totalQuestions,
                'ready' => $answeredQuestions,
        );
    }

    protected function _processPostedValue($key, $value, Webenq_Model_Respondent $respondent)
    {
        // process recursively
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $this->_processPostedValue($k, $v, $respondent);
            }
        }

        // check for range
        if (isset($this->_request->{"$key-1"})) {
            $value = array($value, $this->_request->{"$id-1"});
        }

        // save answer-id or text
        $qq = Doctrine_Core::getTable('Webenq_Model_QuestionnaireQuestion')
        ->find(str_replace('qq_', null, $key));
        if ($value === '' || is_array($value)) {
            $this->_saveEmptyAnswer($qq, $respondent);
        } elseif ($qq->answerPossibilityGroup_id) {
            $this->_saveAnswerId($qq, $value, $respondent);
        } else {
            $this->_saveAnswerText($qq, $value, $respondent);
        }
    }

    /**
     * Removes all answers for the given question and respondent combination
     *
     * @param Webenq_Model_QuestionnaireQuestion $qq
     * @param Webenq_Model_Respondent $respondent
     */
    protected function _removeAnswers(Webenq_Model_QuestionnaireQuestion $qq, Webenq_Model_Respondent $respondent)
    {
        Doctrine_Query::create()
        ->delete('Webenq_Model_Answer a')
        ->where('a.respondent_id = ?', $respondent->id)
        ->andWhere('a.questionnaire_question_id = ?', $qq->id)
        ->execute();
    }

    protected function _saveEmptyAnswer(Webenq_Model_QuestionnaireQuestion $qq, Webenq_Model_Respondent $respondent)
    {
        $this->_removeAnswers($qq, $respondent);
        try {
            $answer = new Webenq_Model_Answer();
            $answer->respondent_id = $respondent->id;
            $answer->questionnaire_question_id = $qq->id;
            $answer->save();
        } catch(Exception $e) {
            return false;
        }
        return true;
    }

    protected function _saveAnswerId(Webenq_Model_QuestionnaireQuestion $qq, $answerIds,
            Webenq_Model_Respondent $respondent)
    {
        $this->_removeAnswers($qq, $respondent);

        if (!is_array($answerIds)) {
            $answerIds = array($answerIds);
        }

        foreach ($answerIds as $answerId) {
            try {
                $answer = new Webenq_Model_Answer();
                $answer->answerPossibility_id = $answerId;
                $answer->respondent_id = $respondent->id;
                $answer->questionnaire_question_id = $qq->id;
                $answer->save();
            } catch(Exception $e) {
                return false;
            }
        }
        return true;
    }

    protected function _saveAnswerText(Webenq_Model_QuestionnaireQuestion $qq, $answerValues,
            Webenq_Model_Respondent $respondent)
    {
        $this->_removeAnswers($qq, $respondent);

        if (!is_array($answerValues)) {
            $answerValues = array($answerValues);
        }

        foreach ($answerValues as $answerValue) {
            try {
                $answer = new Webenq_Model_Answer();
                $answer->text = $answerValue;
                $answer->respondent_id = $respondent->id;
                $answer->questionnaire_question_id = $qq->id;
                $answer->save();
            } catch (Exception $e) {
                return false;
            }
        }
        return true;
    }

    public function groupAction()
    {
        $questions = Doctrine_Query::create()
        ->from('Webenq_Model_CollectionPresentation cp')
        ->innerJoin('cp.QuestionnaireQuestion qq')
        ->innerJoin('qq.Questionnaire q')
        ->where('q.id = ?', $this->_request->id)
        ->andWhere('cp.parent_id IS NULL')
        ->groupBy('qq.id')
        ->execute();

        $groups = Doctrine_Query::create()
        ->from('Webenq_Model_CollectionPresentation cp')
        ->innerJoin('cp.QuestionnaireQuestion qq')
        ->innerJoin('qq.Questionnaire q')
        ->where('q.id = ?', $this->_request->id)
        ->andWhere('cp.parent_id IS NULL')
        //            ->groupBy('cp.parent_id')
        ->execute();

        $this->view->questions = $questions;
        $this->view->groups = $groups;
    }

    public function downloadAction()
    {
        if (!$id = $this->_request->id) {
            throw new Exception('No ID given!');
        }

        $form = new Zend_Form();
        $form->addElements(
            array(
                $form->createElement(
                    'radio',
                    'format',
                    array(
                        'label' => 'Selecteer een formaat:',
                        'multiOptions' => array(
                            'xls' => 'Microsoft Excel 5 (XLS)',
                            'xlsx' => 'Microsoft Excel 2007 (XLSX)',
                            'csv' => 'comma separated values (CSV)',
                            'ods' => 'Open Office Spreadsheet (ODS)',
                            //'pdf' => 'Portable Document Format (PDF)',
                        ),
                        'required' => true,
                    )
                ),
                $form->createElement(
                    'submit',
                    'submit',
                    array(
                        'label' => 'Download'
                    )
                ),
            )
        );

        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getPost())) {

                // disable layout and view renderer
                $this->_helper->layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender();

                $format = $form->format->getValue();
                $questionnaire = Webenq_Model_Questionnaire::getQuestionnaire(
                    $id, $this->_helper->language(), null, null, true
                );
                $download = Webenq_Download::factory($format, $questionnaire);
                $download->send($this->_response);

                return;
            }
        }

        // view
        $this->view->id = $id;
        $this->view->form = $form;
    }

    public function printAction()
    {
        if (!$id = $this->_request->id) {
            throw new Exception('No ID given!');
        }

        /* disable view renderer */
        $this->_helper->viewRenderer->setNoRender();

        $form = new Zend_Form();
        $form->addElements(
            array(
                $form->createElement(
                    'radio', 'format', array(
                        'label' => 'Selecteer een formaat:',
                        'multiOptions' => array(
                            'pdf' => 'Portable Document Format (PDF)',
                        ),
                        'required' => true,
                    )
                ),
                $form->createElement(
                    'submit',
                    'submit',
                    array(
                        'label' => 'Download'
                    )
                ),
            )
        );

        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getPost())) {

                /* disable layout */
                $this->_helper->layout->disableLayout();

                $format = $form->format->getValue();
                $questionnaire = Questionnaire::getQuestionnaire($id, $this->_helper->language(), null, null, false);
                $download = Webenq_Print::factory($format, $questionnaire);
                $download->send($this->_response);

                return;
            }
        }

        /* display form */
        $this->_response->setBody($form->render());
    }
}
