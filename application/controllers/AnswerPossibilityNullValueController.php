<?php
/**
 * Controller class
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class AnswerPossibilityNullValueController extends Zend_Controller_Action
{
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
        $this->_language = Zend_Registry::get('Zend_Locale')->getLanguage();
    }

    /**
     * Renders the overview of question types
     *
     * @return void
     */
    public function indexAction()
    {
        /* get answer possibility groups */
        $answerPossibilityNullValues = Doctrine_Query::create()
            ->from('Webenq_Model_AnswerPossibilityNullValue apnv')
            ->orderBy('apnv.value')
            ->execute();

        /* render view */
        $this->view->answerPossibilityNullValues = $answerPossibilityNullValues;
    }

    /**
     * Handles the adding of an answer-possibility-group
     *
     * @return void
     */
    public function addAction()
    {
        /* add index-action to stack */
        $this->_helper->actionStack('index', 'answer-possibility-null-value');

        /* get form */
        $form = new Webenq_Form_AnswerPossibilityNullValue_Add();

        /* process posted data */
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $answerPossibilityNullValue = new AnswerPossibilityNullValue();
                $answerPossibilityNullValue->fromArray($form->getValues());
                $answerPossibilityNullValue->save();
                $this->_redirect('/answer-possibility-group');
            }
        }

        /* render view */
        $this->view->form = $form;
    }

    /**
     * Handles the editing of an answer-possibility-group
     *
     * @return void
     */
    public function editAction()
    {
        /* add index-action to stack */
        $this->_helper->actionStack('index', 'answer-possibility-null-value');

        /* get record */
        $answerPossibilityNullValue = Doctrine_Core::getTable('Webenq_Model_AnswerPossibilityNullValue')
            ->find($this->_request->id);

        /* get form */
        $form = new Webenq_Form_AnswerPossibilityNullValue_Edit($answerPossibilityNullValue);

        /* process posted data */
        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                $answerPossibilityNullValue->fromArray($form->getValues());
                $answerPossibilityNullValue->save();
                $this->_redirect('/answer-possibility-group');
            }
        }

        /* render view */
        $this->view->form = $form;
        $this->view->answerPossibilityNullValue = $answerPossibilityNullValue;
    }

    /**
     * Handles the deleting of an answer-possibility-group
     *
     * @return void
     */
    public function deleteAction()
    {
        $this->_helper->actionStack('index', 'answer-possibility-null-value');

        /* get record */
        $answerPossibilityNullValue = Doctrine_Core::getTable('Webenq_Model_AnswerPossibilityNullValue')
            ->find($this->_request->id);

        /* get form */
        $form = new Webenq_Form_Confirm(
            $answerPossibilityNullValue->id,
            'Weet u zeker dat u nulwaarde-antwoordmogelijkheid "' . $answerPossibilityNullValue->value .
                '" wilt verwijderen?'
        );

        /* process posted data */
        if ($this->_request->isPost()) {
            if ($this->_request->yes) {
                $answerPossibilityNullValue->delete();
            }
            $this->_redirect('/answer-possibility-group');
        }

        /* render view */
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->form = $form;
        $this->_response->setBody($this->view->render('confirm.phtml'));
    }
}