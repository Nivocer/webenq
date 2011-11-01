<?php
/**
 * Controller class
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class AnswerPossibilitySynonymController extends Zend_Controller_Action
{
    public function addAction()
    {
        /* get possibility */
        $answerPossibilityText = Doctrine_Core::getTable('Webenq_Model_AnswerPossibilityText')
            ->find($this->_request->id);

        // get form
        $form = new Webenq_Form_AnswerPossibilitySynonym_Add($answerPossibilityText);

        // process posted data
        if ($this->_helper->form->isPostedAndValid($form)) {

            // store synonym
            $answerPossibilityTextSynonym = new Webenq_Model_AnswerPossibilityTextSynonym();
            $answerPossibilityTextSynonym->fromArray($form->getValues());

            try {
                $answerPossibilityTextSynonym->save();
                $id = $answerPossibilityTextSynonym->AnswerPossibilityText->AnswerPossibility->id;
                $this->_redirect("answer-possibility/edit/id/$id");
            }
            catch (Exception $e) {
                   $form->text->addError($e->getMessage());
            }
        }

        /* render view */
        $this->view->form = $form;
        $this->view->answerPossibilityText = $answerPossibilityText;
    }

    public function editAction()
    {
        /* get synonym */
        $synonym = Doctrine_Core::getTable('Webenq_Model_AnswerPossibilityTextSynonym')
            ->find($this->_request->id);

        /* get form */
        $form = new Webenq_Form_AnswerPossibilitySynonym_Edit($synonym);

        /* process posted data */
        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if ($form->isValid($data)) {

                /* store synonym */
                $synonym->fromArray($data);

                try {
                    $synonym->save();
                    $id = $synonym->AnswerPossibilityText->AnswerPossibility->id;
                    $this->_redirect("/answer-possibility/edit/id/$id");
                }
                catch (Exception $e) {
                       $form->text->addError($e->getMessage());
                }
            }
        }

        /* assign to view */
        $this->view->form = $form;
        $this->view->synonym = $synonym;
    }

    /**
     * Handles the deleting of an answer-possibility-synonym
     *
     * @return void
     */
    public function deleteAction()
    {
        /* get synonym */
        $synonym = Doctrine_Core::getTable('Webenq_Model_AnswerPossibilityTextSynonym')
            ->find($this->_request->id);

        /* get form */
        $form = new Webenq_Form_Confirm(
            $synonym->id,
            'Weet u zeker dat u het synoniem "' . $synonym->text . '" wilt verwijderen?'
        );

        /* process posted data */
        if ($this->_request->isPost()) {
            if ($this->_request->yes) {
                $synonym->delete();
            }
            $this->_redirect('/answer-possibility/edit/id/' . $synonym->AnswerPossibilityText->AnswerPossibility->id);
        }

        /* render view */
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->form = $form;
        $this->_response->setBody($this->view->render('confirm.phtml'));
    }
}