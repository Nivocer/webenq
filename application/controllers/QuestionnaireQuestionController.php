<?php
/**
 * Controller class
 *
 * @category    Webenq
 * @package        Controllers
 * @author        Bart Huttinga <b.huttinga@nivocer.com>
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
    public function init() {
        $this->_helper->ajaxContext()->initContext();
        $this->_language = Zend_Registry::get('language');
    }
    /**
     * Renders the form for adding an existing question to a questionnaire
     */
    public function addAction() {
        $questionnaireId = $this->_request->questionnaire_id;
        if (!$questionnaireId) {
            throw new Exception('No questionnaire id given!');
        }
        $form = new Webenq_Form_QuestionnaireQuestion_Add($questionnaireId);
        $form->setAction($this->view->baseUrl('/questionnaire-question/add'));
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                /* store */
                $qq = new QuestionnaireQuestion();
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
            ->from('Question q')
            ->innerJoin('q.QuestionText qt')
            ->where('qt.language = ?', $this->_language)
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        $this->view->form = $form;
        $this->view->questions = $questions;
    }
    /**
     * Renders the form for editing a questionnaire
     *
     * @return void
     */
    public function editAction() {
        $questionnaireQuestion = Doctrine_Core::getTable('QuestionnaireQuestion')->find($this->_request->id);
        $form = new Webenq_Form_QuestionnaireQuestion_Edit($questionnaireQuestion);
        $form->setAction($this->view->baseUrl($this->_request->getPathInfo()));
        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if ($form->isValid($data)) {
                $form->storeValues();
                if ($this->_request->isXmlHttpRequest()) {
                    $this->_helper->json(array('reload' => true,));
                } else {
                    $this->_redirect('questionnaire/edit/id/' . $questionnaireQuestion->Questionnaire->id);
                }
            }
        }
        $this->view->repositoryQuestions = Doctrine_Query::create()
            ->from('QuestionnaireQuestion qq')
            ->innerJoin('qq.CollectionPresentation cp')
            ->where('qq.id != ?', $questionnaireQuestion->id)
            ->andWhere('cp.parent_id IS NULL')
            ->execute();
        $this->view->form = $form;
        $this->view->questionnaireQuestion = $questionnaireQuestion;
        $this->view->cols = $cols = 1 + $questionnaireQuestion->CollectionPresentation[0]
            ->CollectionPresentation[0]
            ->CollectionPresentation
            ->count();
        $this->view->subQuestions = $this->_getSubQuestions($questionnaireQuestion);
    }
    /**
     * Renders the form for deleting a questionnaire from a questionnaire,
     * or - optionally completely deleting it from the repository.
     *
     * @return void
     */
    public function deleteAction() {
        $questionnaireQuestion = Doctrine_Query::create()
            ->from('QuestionnaireQuestion qq')
            ->innerJoin('qq.Question q')
            ->leftJoin('q.QuestionText qt')
            ->where('qq.id = ?', $this->_request->id)
            ->andWhere('qt.language = ?', $this->_language)
            ->execute()
            ->getFirst();
        $form = new Webenq_Form_QuestionnaireQuestion_Delete($questionnaireQuestion);
        $form->setAction($this->view->baseUrl($this->_request->getPathInfo()));
        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if (isset($data['yes'])) {
                if ($data['change_globally'] == 'global') {
                    $questionnaireQuestion->Question->delete();
                } else {
                    $questionnaireQuestion->delete();
                }
                if ($this->_request->isXmlHttpRequest()) {
                    $this->_helper->json(array('reload' => true,));
                }
            } else {
                if ($this->_request->isXmlHttpRequest()) {
                    $this->_helper->json(array('reload' => false,));
                }
            }
        }
        $this->view->form = $form;
        $this->view->questionnaireQuestion = $questionnaireQuestion;
    }
    protected function _getSubQuestions(QuestionnaireQuestion $questionnaireQuestion) {
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
    public function saveStateAction() {
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
    protected function _saveGridSubquestions($parentId, array $grid) {
        /* get collection-presentation object for given parent */
        $cp = Doctrine_Core::getTable('QuestionnaireQuestion')->find($parentId)->CollectionPresentation->getFirst();
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
                $current = Doctrine_Core::getTable('QuestionnaireQuestion')
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
    public function addSubquestionAction() {
        $qq = Doctrine_Query::create()
            ->from('QuestionnaireQuestion qq')
            ->innerJoin('qq.CollectionPresentation cp')
            ->where('qq.id != ?', $this->_request->id)
            ->andWhere('cp.parent_id IS NULL')
            ->execute();
        $this->view->qq = $qq;
    }
}
