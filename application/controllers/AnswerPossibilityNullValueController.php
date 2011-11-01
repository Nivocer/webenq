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
        // get answer possibility groups
        $answerPossibilityNullValues = Doctrine_Query::create()
            ->from('Webenq_Model_AnswerPossibilityNullValue apnv')
            ->orderBy('apnv.value')
            ->execute();

        // render view
        $this->view->answerPossibilityNullValues = $answerPossibilityNullValues;
    }

    /**
     * Handles the adding of an answer-possibility-group
     *
     * @return void
     */
    public function addAction()
    {
        // get form
        $form = new Webenq_Form_AnswerPossibilityNullValue_Add();

        // process posted data
        if ($this->_helper->form->isPostedAndValid($form)) {
            $answerPossibilityNullValue = new Webenq_Model_AnswerPossibilityNullValue();
            $answerPossibilityNullValue->fromArray($form->getValues());
            $answerPossibilityNullValue->save();
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
        // get record
        $answerPossibilityNullValue = Doctrine_Core::getTable('Webenq_Model_AnswerPossibilityNullValue')
            ->find($this->_request->id);

        // get form
        $form = new Webenq_Form_AnswerPossibilityNullValue_Edit($answerPossibilityNullValue);

        // process posted data
        if ($this->_helper->form->isPostedAndValid($form)) {
            $answerPossibilityNullValue->fromArray($form->getValues());
            $answerPossibilityNullValue->save();
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
        // get record
        $answerPossibilityNullValue = Doctrine_Core::getTable('Webenq_Model_AnswerPossibilityNullValue')
            ->find($this->_request->id);

        // get form
        $form = new Webenq_Form_Confirm(
            $answerPossibilityNullValue->id,
            'Weet u zeker dat u nulwaarde-antwoordmogelijkheid "' . $answerPossibilityNullValue->value .
                '" wilt verwijderen?'
        );

        /* process posted data */
        if ($this->_helper->form->isPostedAndValid($form)) {
            if ($this->_request->yes) {
                $answerPossibilityNullValue->delete();
                $this->_helper->json(array('reload' => true));
            }
            $this->_helper->json(array('reload' => false));
        }

        // render view
        $this->_helper->form->render($form);
    }
}