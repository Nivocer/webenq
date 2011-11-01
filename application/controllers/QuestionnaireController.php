<?php
/**
 * Controller class
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class QuestionnaireController extends Zend_Controller_Action
{
    /**
     * Controller actions that are ajaxable
     *
     * @var array
     */
    public $ajaxable = array(
        'add' => array('html'),
    );

    /**
     * Renders the overview of questoinnaires
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->questionnaires = Doctrine_Query::create()
            ->select('q.*, COUNT(qq.id) as count_qqs')
            ->from('Webenq_Model_Questionnaire q')
            ->leftJoin('q.QuestionnaireQuestion qq')
            ->groupBy('q.id')
            ->execute();
    }

    /**
     * Renders an xform
     *
     * @return void
     */
    public function xformAction()
    {
        $questionnaire = Webenq_Model_Questionnaire::getQuestionnaire($this->_request->id, $this->_helper->language());

        $filename = preg_replace('/[^A-Za-z0-9-]/', null, implode('-', array(
            $questionnaire->id, $questionnaire->getQuestionnaireTitle()->title, date('YmdHis'))))
            . '-xform.xml';

        $xml = $questionnaire->getXform();

        $this->_helper->layout->disableLayout();
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

        $filename = preg_replace('/[^A-Za-z0-9-]/', null, implode('-', array(
            $questionnaire->id, $questionnaire->getQuestionnaireTitle()->title, date('YmdHis'))))
            . '-xform-data.xml';

        $xml = $questionnaire->getXformData();

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_response
            ->setHeader('Content-Type', 'text/xml; charset=utf-8')
            ->setHeader('Content-Transfer-Encoding', 'binary')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($xml->saveXML());
    }

    /**
     * Renders the form for adding a questionnaire
     *
     * @return void
     */
    public function addAction()
    {
        $form = new Webenq_Form_Questionnaire_Add();
        $form->setAction($this->_request->getRequestUri());

        if ($this->_helper->form->isPostedAndValid($form)) {
            $questionnaire = new Webenq_Model_Questionnaire();
            $questionnaire->fromArray($form->getValues());
            $questionnaire->meta = serialize(array('timestamp' => time()));
            $questionnaire->save();
            $this->_helper->json(array('reload' => true));
        }

        $this->view->form = $form;
    }

    /**
     * Renders the form for editing a questionnaire
     *
     * @return void
     */
    public function editAction()
    {
        $questionnaire = Webenq_Model_Questionnaire::getQuestionnaire($this->_request->id, $this->_helper->language());
        if (!$questionnaire) $this->_redirect('questionnaire');

        $form = new Webenq_Form_Questionnaire_Edit($questionnaire);

        if ($this->_helper->form->isPostedAndValid($form)) {
            $questionnaire->fromArray($form->getValues());
            $questionnaire->save();
            $this->_redirect($this->_request->getPathInfo());
        }

        $this->view->form = $form;
        $this->view->questionnaire = $questionnaire;
        $this->view->totalPages = Webenq_Model_Questionnaire::getTotalPages($questionnaire['id']);
    }

    public function orderAction()
    {
        /* disable view/layout rendering */
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout(true);

        if ($this->_request->data) {
            $this->_orderPagesAndQuestions(Zend_Json::decode($this->_request->data));
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
        $qq->ReportPresentation[] = new ReportPresentation();
        $qq->save();

        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout(true);
        $this->_response->setBody($qq->id);
    }

    /**
     * Renders the confirmation form for deleting a questionnaire
     *
     * @return void
     */
    public function deleteAction()
    {
        $this->_helper->actionStack('index', 'questionnaire');

        $questionnaire = Doctrine_Core::getTable('Webenq_Model_Questionnaire')
            ->find($this->_request->id);

        $confirmationText = 'Weet u zeker dat u questionnaire ' . $questionnaire->id .
            ' (inclusief alle vragen en antwoorden) wilt verwijderen?';

        $form = new Webenq_Form_Confirm($questionnaire->id, $confirmationText);

        /* process posted data */
        if ($this->_request->isPost()) {
            if ($this->_request->yes) {
                $questionnaire->delete();
            }
            $this->_redirect('/questionnaire');
        }

        /* render view */
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->form = $form;
        $this->_response->setBody($this->view->render('confirm.phtml'));
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
            $this->_request->id, $this->_helper->language(), $page, $respondent, true);
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

    /**
     * Reports the collected data for the given questionnaire
     *
     * @return void
     */
    public function reportAction()
    {
        /* get questions for current page */
        $pageNr = $this->_request->page ? $this->_request->page : null;
        $questionnaire = Webenq_Model_Questionnaire::getQuestionnaire($this->_request->id, $this->_helper->language(), $pageNr, null, true);

        /* display */
        $this->view->language = $this->_helper->language();
        $this->view->questionnaire = $questionnaire;
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
        $form->addElements(array(
            $form->createElement('radio', 'format', array(
                'label' => 'Selecteer een formaat:',
                'multiOptions' => array(
                    'xls' => 'Microsoft Excel 5 (XLS)',
                    'xlsx' => 'Microsoft Excel 2007 (XLSX)',
                    'csv' => 'comma separated values (CSV)',
                    'ods' => 'Open Office Spreadsheet (ODS)',
//                    'pdf' => 'Portable Document Format (PDF)',
                ),
                'required' => true,
            )),
            $form->createElement('submit', 'submit', array('label' => 'Download')),
        ));

        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getPost())) {

                // disable layout and view renderer
                $this->_helper->layout->disableLayout();
                $this->_helper->viewRenderer->setNoRender();

                $format = $form->format->getValue();
                $questionnaire = Webenq_Model_Questionnaire::getQuestionnaire($id, $this->_helper->language(), null, null, true);
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
        $form->addElements(array(
            $form->createElement('radio', 'format', array(
                'label' => 'Selecteer een formaat:',
                'multiOptions' => array(
                    'pdf' => 'Portable Document Format (PDF)',
                ),
                'required' => true,
            )),
            $form->createElement('submit', 'submit', array('label' => 'Download')),
        ));

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

    public function jrxmlAction()
    {
        $questionnaire = Webenq_Model_Questionnaire::getQuestionnaire($this->_request->id, $this->_helper->language());

        $filename = preg_replace('/[^A-Za-z0-9-]/', null, implode('-', array(
            $questionnaire->id, $questionnaire->getQuestionnaireTitle()->title, date('YmdHis'))))
            . '.jrxml';

        // config settings
        // @todo get this from database
        $this->view->pageWidth = 595;
        $this->view->pageHeight = 842;

        $this->render();

        // create new dom document
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        // append rendered xml to dom
        $fragment = $dom->createDocumentFragment();
        $fragment->appendXML($this->_response->getBody());
        $dom->appendChild($fragment);

        // output
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $this->_response
            ->setHeader('Content-Type', 'text/xml; charset=utf-8')
            ->setHeader('Content-Transfer-Encoding', 'binary')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dom->saveXML());
    }
}