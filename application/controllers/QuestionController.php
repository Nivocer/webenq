<?php
/**
 * Controller class
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class QuestionController extends Zend_Controller_Action
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
     * Current question
     *
     * @var Question
     */
    protected $_question;

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
     * Renders the overview of question types
     *
     * @return void
     */
    public function indexAction()
    {
        /* get questionnaire */
        $questions = Doctrine_Query::create()
            ->from('Question q')
            ->innerJoin('q.QuestionText qt')
            ->where('qt.language = ?', $this->_language)
            ->orderBy('q.created DESC, qt.text')
            ->execute();
        $this->view->questions = $questions;
    }

    /**
     * Renders the form for adding a question
     *
     * @return void
     */
    public function addAction()
    {
        // get form, set action and add questionnaire_id if known
        $form = new Webenq_Form_Question_Add();
        $form->setAction($this->view->baseUrl('question/add'));
        if ($this->_request->questionnaire_id) {
            $form->addElement($form->createElement('hidden', 'questionnaire_id', array(
                'value' => $this->_request->questionnaire_id)));
        }

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {

            // get clean values
            $values = $form->getValues();

            // create question from text fields
            $question = new Webenq_Model_Question_Open_Text();
            $question->setQuestionTexts($values['text']);
            $question->save();

            /* if a questionnaire id is posted, connect question to it */
            if ($values['questionnaire_id']) {

                $qq = new Webenq_Model_QuestionnaireQuestion();
                $qq->questionnaire_id = $this->_request->questionnaire_id;
                $qq->question_id = $question->id;

                $cp = new Webenq_Model_CollectionPresentation();
                $cp->weight = -1;
                $qq->CollectionPresentation[] = $cp;

                $qq->ReportPresentation[] = new Webenq_Model_ReportPresentation();
                $qq->save();
            }

            if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->json(array(
                    'id' => $question->id,
                    'reload' => true,
                ));
            } else {
                $this->_redirect('question');
            }
        }

        $this->view->form = $form;
    }

    /**
     * Renders the form for editing a question
     *
     * @return void
     */
    public function editAction()
    {
        $question = Doctrine_Core::getTable('Question')
            ->find($this->_request->id);

        $form = new Webenq_Form_Question_Edit($question);
        $form->setAction($this->view->baseUrl('/question/edit/id/' . $this->_request->id));

        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
            $values = $form->getValues();
            foreach ($values['text'] as $language => $text) {
                // get existing question-text
                $questionText = Doctrine_Core::getTable('QuestionText')
                    ->findOneByQuestionIdAndLanguage($question->id, $language);
                // or create new question-text
                if (!$questionText) {
                    $questionText = new Webenq_Model_QuestionText();
                    $questionText->question_id = $question->id;
                    $questionText->language = $language;
                }
                // set (new) text and save
                $questionText->text = $text;
                $questionText->save();
            }
            if ($this->_request->isXmlHttpRequest()) {
                $this->_helper->json(array('reload' => true));
            } else {
                $this->_redirect('/question');
            }
        }

        $this->view->form = $form;
    }

    /**
     * Renders the confirmation form for deleting a question
     *
     * @return void
     */
    public function deleteAction()
    {
        $question = Doctrine_Core::getTable('Question')
            ->find($this->_request->id);

        $confirmationText = 'Weet u zeker dat u de vraag "' . $question->QuestionText[0]->text .
            '" (inclusief alle vertalingen) wilt verwijderen?';

        $form = new Webenq_Form_Confirm($question->id, $confirmationText);
        $form->setAction($this->view->baseUrl('/question/delete/id/' . $this->_request->id));

        /* process posted data */
        if ($this->_request->isPost()) {
            if ($this->_request->yes) {
                   $question->delete();
            }
            if ($this->_request->isXmlHttpRequest()) {
                if ($this->_request->yes) {
                    $this->_helper->json(array('reload' => true));
                } else {
                    $this->_helper->json(array('reload' => false));
                }
            } else {
                $this->_redirect('/question');
            }
        }

        /* render view */
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->form = $form;
        $this->_response->setBody($this->view->render('confirm.phtml'));
    }

    public function autocompleteAction()
    {
        /* get term and language (from element name) */
        $term = $this->_request->term;
        $elm = preg_match('/\[(.{2})\]$/', $this->_request->element, $matches);
        $lang = $matches[1];

        /* return results */
        $this->_helper->json(Question::autocomplete($term, $lang));
    }
}