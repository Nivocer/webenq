<?php
/**
 * Controller class
 *
 * @category    Webenq
 * @package        Controllers
 * @author        Bart Huttinga <b.huttinga@nivocer.com>
 */
class QuestionnaireController extends Zend_Controller_Action
{
    /**
     * Controller actions that are ajaxable
     *
     * @var array
     */
    public $ajaxable = array(
    );

    /**
     * Current language
     *
     * @var string
     */
    protected $_language;

    /**
     * Initializes the class
     *
     * @return void
     */
    public function init()
    {
        $this->_helper->ajaxContext()->initContext();
        $this->_language = Zend_Registry::get('language');
    }

    /**
     * Renders the overview of questoinnaires
     *
     * @return void
     */
    public function indexAction()
    {
        $this->view->questionnaires = Doctrine_Query::create()
            ->select('q.*, COUNT(qq.id) as count_qqs')
            ->from('Questionnaire q')
            ->leftJoin('q.QuestionnaireQuestion qq')
            ->groupBy('q.id')
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    }

    /**
     * Renders the form for adding a questionnaire
     *
     * @return void
     */
    public function addAction()
    {
        $this->_helper->actionStack('index', 'questionnaire');

        $form = new Webenq_Form_Questionnaire_Add();

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            $questionnaire = new Webenq_Model_Questionnaire();
            $questionnaire->fromArray($form->getValues());
            $questionnaire->meta = serialize(array('timestamp' => time()));
            $questionnaire->save();
            $this->_redirect('/questionnaire');
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
        $questionnaire = Questionnaire::getQuestionnaire($this->_request->id, $this->_language);
        if (!$questionnaire) $this->_redirect('questionnaire');

        $form = new Webenq_Form_Questionnaire_Edit($questionnaire);

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            $questionnaire->fromArray($form->getValues());
            $questionnaire->save();
            $this->_redirect($this->_request->getPathInfo());
        }

        $questionsToBeRendered = array();
        foreach ($questionnaire['QuestionnaireQuestion'] as $qq) {
            $questionsToBeRendered[] = $qq;
        }

        $this->view->form = $form;
        $this->view->questionnaire = $questionnaire;
        $this->view->questions = $questionsToBeRendered;
        $this->view->totalPages = Questionnaire::getTotalPages($questionnaire['id']);
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
        if (count($data) === 0) {
            return;
        }

        foreach ($data as $key => $val) {

            $page = $key + 1;

            $qqIds = array();
            foreach ($val as $id) {
                $id = (int) str_replace('qq_', null, $id);
                $qqIds[] = $id;
            }

            // reset all questions on this page
            Doctrine_Query::create()
                ->update('CollectionPresentation cp')
                ->set('weight', '?', 0)
                ->set('page', '?', $page)
                ->whereIn('cp.questionnaire_question_id', $qqIds)
                ->execute();

            // get questions on this page
            $qqs = Doctrine_Query::create()
                ->from('QuestionnaireQuestion qq')
                ->leftJoin('qq.CollectionPresentation cp')
                ->whereIn('qq.id', $qqIds)
                ->execute();

            // set new weight
            foreach ($qqs as $weight => $qq) {
                $qq->CollectionPresentation[0]->weight = array_search($qq->id, $qqIds);
                $qq->save();
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
            ->update('CollectionPresentation cp')
            ->set('weight', '?', 0)
            ->set('page', '?', 0)
            ->whereIn('cp.questionnaire_question_id', $qqIds)
            ->execute();

        // get subquestions
        $qqs = Doctrine_Query::create()
            ->from('QuestionnaireQuestion qq')
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
        $qq = new QuestionnaireQuestion();
        $qq->questionnaire_id = $this->_request->questionnaire_id;
        $qq->question_id = $this->_request->question_id;
        $cp = new CollectionPresentation();
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

        $questionnaire = Doctrine_Core::getTable('Questionnaire')
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
        /* get session */
        $session = new Zend_Session_Namespace();

        /* reset respondent in session if other questionnaire */
        if ($session->questionnaire_id != $this->_request->id) {
            $session->respondent_id = null;
        }

        /* set respondent */
        if ($this->_request->respondent_id) {
            $respondent = Doctrine_Core::getTable('Respondent')
                ->find($this->_request->respondent_id);
        } else if ($session->respondent_id) {
            $respondent = Doctrine_Core::getTable('Respondent')
                ->find($session->respondent_id);
        } else {
            $respondent = new Webenq_Model_Respondent();
            $respondent->questionnaire_id = $this->_request->id;
            $respondent->save();
        }

        /* store respondent id to session and reload page */
        $session->respondent_id = $respondent->id;
        $session->questionnaire_id = $this->_request->id;

        try {
            /* get current page */
            $pageNr = Doctrine_Query::create()
                ->from('QuestionnaireQuestion qq')
                ->leftJoin('qq.Answer a ON a.questionnaire_question_id = qq.id AND a.respondent_id = ?',
                    $respondent->id)
                ->innerJoin('qq.CollectionPresentation cp')
                ->where('a.id IS NULL')
                ->andWhere('qq.questionnaire_id = ?', $this->_request->id)
                ->orderBy('cp.page')
                ->groupBy('cp.page')
                ->limit(1)
                ->execute()
                ->getFirst()->CollectionPresentation[0]->page;
        } catch (Exception $e) {
            /* redirect if no more questions */
            $this->_redirect('/questionnaire');
        }

        /* get questions for current page */
        $questionnaire = Questionnaire::getQuestionnaire($this->_request->id, $this->_language, $pageNr, $respondent);
        $qqs = $questionnaire['QuestionnaireQuestion'];

        /* redirect if no more questions */
        if (!isset($qqs[0])) $this->_redirect('/questionnaire');

        /* get form */
        $form = new Webenq_Form_Questionnaire_Collect($qqs);

        /* get progress data */
        $totalQuestions = (int) Doctrine_Query::create()
            ->select('COUNT(qq.id) AS count')
            ->from('QuestionnaireQuestion qq')
            ->where('qq.questionnaire_id = ?', $this->_request->id)
            ->execute()->getFirst()->count;

        $answeredQuestions = (int) Doctrine_Query::create()
            ->select('COUNT(qq.id) AS count')
            ->from('QuestionnaireQuestion qq')
            ->leftJoin('qq.Answer a ON a.questionnaire_question_id = qq.id AND a.respondent_id = ?', $respondent->id)
            ->where('a.id IS NOT NULL')
            ->andWhere('qq.questionnaire_id = ?', $this->_request->id)
            ->execute()->getFirst()->count;

        /* process posted data */
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                // iterate filtered/validated values
                foreach ($form->getValues() as $id => $value) {
                    // check for range
                    if (isset($this->_request->{"$id-1"})) {
                        $value = array($value, $this->_request->{"$id-1"});
                    }

                    // save answer-id or text
                    $qq = Doctrine_Core::getTable('QuestionnaireQuestion')
                        ->find(str_replace('qq_', null, $id));
                    if ($qq->answerPossibilityGroup_id) {
                        $this->_saveAnswerId($qq, $value, $respondent);
                    } else {
                        $this->_saveAnswerText($qq, $value, $respondent);
                    }
                }

                /* reload page */
                $this->_redirect($this->_request->getPathInfo());
            }
        }

        /* display form */
        $this->view->form = $form;
        $this->view->pageNr = $pageNr;
        $this->view->progress = array(
            'total' => $totalQuestions,
            'ready' => $answeredQuestions,
        );
    }

    protected function _saveAnswerId(QuestionnaireQuestion $qq, $answerIds, Respondent $respondent)
    {
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

    protected function _saveAnswerText(QuestionnaireQuestion $qq, $answerValues, Respondent $respondent)
    {
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
        $questionnaire = Questionnaire::getQuestionnaire($this->_request->id, $this->_language, $pageNr, null, true);

        /* display */
        $this->view->questionnaire = $questionnaire;
    }

    public function groupAction()
    {
        $questions = Doctrine_Query::create()
            ->from('CollectionPresentation cp')
            ->innerJoin('cp.QuestionnaireQuestion qq')
            ->innerJoin('qq.Questionnaire q')
            ->where('q.id = ?', $this->_request->id)
            ->andWhere('cp.parent_id IS NULL')
            ->groupBy('qq.id')
            ->execute();

        $groups = Doctrine_Query::create()
            ->from('CollectionPresentation cp')
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

        /* disable view renderer */
        $this->_helper->viewRenderer->setNoRender();

        $form = new Zend_Form();
        $form->addElements(array(
            $form->createElement('radio', 'format', array(
                'label' => 'Selecteer een formaat:',
                'multiOptions' => array(
                    'xls' => 'Microsoft Excel 5 (XLS)',
                    'xlsx' => 'Microsoft Excel 2007 (XLSX)',
                    'csv' => 'comma separated values (CSV)',
                    'ods' => 'Open Office Spreadsheet (ODS)',
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
                $questionnaire = Questionnaire::getQuestionnaire($id, $this->_language, null, null, true);
                $download = Webenq_Download::factory($format, $questionnaire);
                $download->send($this->_response);

                return;
            }
        }

        /* display form */
        $this->_response->setBody($form->render());
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
                $questionnaire = Questionnaire::getQuestionnaire($id, $this->_language, null, null, false);
                $download = Webenq_Print::factory($format, $questionnaire);
                $download->send($this->_response);

                return;
            }
        }

        /* display form */
        $this->_response->setBody($form->render());
    }
}