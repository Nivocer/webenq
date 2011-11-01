<?php
/**
 * Controller class
 *
 * @package     Webenq
 * @subpackage  Controllers
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class AnswerPossibilityController extends Zend_Controller_Action
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
        $form = new Webenq_Form_AnswerPossibility_Add($answerPossibilityGroup, $this->_language);

        // process posted data
        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {

            $answerPossibilityText = new Webenq_Model_AnswerPossibilityText();
            $answerPossibilityText->fromArray($form->getValues());

            $answerPossibility = new Webenq_Model_AnswerPossibility();
            $answerPossibility->fromArray($form->getValues());
            $answerPossibility->AnswerPossibilityText[] = $answerPossibilityText;

            try {
                $answerPossibility->save();
                $this->_redirect('/answer-possibility-group/edit/id/' . $answerPossibilityGroup->id);
            }
            catch (Exception $e) {
                $form->value->addError($e->getMessage());
            }
        }

        // render view
        $this->view->form = $form;
        $this->view->answerPossibilityGroup = $answerPossibilityGroup;
    }

    /**
     * Handles the editing of an answer-possibility
     *
     * @return void
     */
    public function editAction()
    {
        /* get possibility */
        $answerPossibility = Doctrine_Core::getTable('Webenq_Model_AnswerPossibility')
            ->find($this->_request->id);

        /* get form */
        $form = new Webenq_Form_AnswerPossibility_Edit($answerPossibility, $this->_language);

        /* process posted data */
        if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {

            $errors = array();
            $data = $form->getValues();

            if (key_exists('submitnull', $this->_request->getPost())) {

                // get answer possibility
                $answerPossibility = Doctrine_Query::create()
                    ->from('Webenq_Model_AnswerPossibility ap')
                    ->innerJoin('ap.AnswerPossibilityText apt WITH apt.language = ?', $this->_language)
                    ->where('ap.id = ?', $data['id'])
                    ->execute()
                    ->getFirst();

                // create new null value
                $nullValue = new Webenq_Model_AnswerPossibilityNullValue();
                $nullValue->value = strtolower($answerPossibility->AnswerPossibilityText[0]->text);
                $nullValue->save();

                // remove all answers
                Doctrine_Query::create()
                    ->delete('Webenq_Model_Answer a')
                    ->where('a.answerPossibility_id = ?', $answerPossibility->id)
                    ->execute();

                // get redirect url before deleting possibility
                $url = 'answer-possibility-group/edit/id/' . $answerPossibility->answerPossibilityGroup_id;

                // remove original
                $answerPossibility->delete();

                $this->_redirect($url);

            } elseif (key_exists('submitmove', $this->_request->getPost())) {

                // get original answer possibility
                $originalAnswerPossibility = Doctrine_Query::create()
                    ->from('Webenq_Model_AnswerPossibility ap')
                    ->innerJoin('ap.AnswerPossibilityText apt WITH apt.language = ?', $this->_language)
                    ->where('ap.id = ?', $data['id'])
                    ->execute()
                    ->getFirst();

                // get target answer possibility
                $targetAnswerPossibility = Doctrine_Query::create()
                    ->from('Webenq_Model_AnswerPossibility ap')
                    ->innerJoin('ap.AnswerPossibilityText apt WITH apt.language = ?', $this->_language)
                    ->where('ap.id = ?', $data['answerPossibility_id'])
                    ->execute()
                    ->getFirst();

                // make original synonym of target
                $answerPossibilityTextSynonym = new Webenq_Model_AnswerPossibilityTextSynonym();
                $answerPossibilityTextSynonym->text = strtolower($originalAnswerPossibility->AnswerPossibilityText[0]->text);
                $targetAnswerPossibility->AnswerPossibilityText[0]->AnswerPossibilityTextSynonym[] = $answerPossibilityTextSynonym;
                $targetAnswerPossibility->save();

                // update all answers
                Doctrine_Query::create()
                    ->update('Webenq_Model_Answer a')
                    ->set('a.answerPossibility_id', '?', $targetAnswerPossibility->id)
                    ->where('a.answerPossibility_id = ?', $originalAnswerPossibility->id)
                    ->execute();

                // get redirect url before deleting possibility
                $url = 'answer-possibility-group/edit/id/' . $originalAnswerPossibility->answerPossibilityGroup_id;

                // remove original
                $originalAnswerPossibility->delete();

                $this->_redirect($url);

            } else {
                // store possibility
                $answerPossibility->fromArray($data['edit']);
                try {
                    $answerPossibility->save();
                }
                catch (Exception $e) {
                    $errors[] = $e->getMessage();
                }

                // store texts
                $translations = $data['edit'];
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

                if (count($errors) == 0) {
                    $this->_redirect('/answer-possibility-group/edit/id/' .
                        $answerPossibility->AnswerPossibilityGroup->id);
                } else {
                    $form->value->addErrors($errors);
                }
            }
        }

        /* assign to view */
        $this->view->form = $form;
        $this->view->answerPossibility = $answerPossibility;
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
        $form = new Webenq_Form_Confirm($answerPossibility->id,
            'Weet u zeker dat u het antwoord "' . $answerPossibility->getAnswerPossibilityText()->text .
                '" wilt verwijderen?'
        );

        /* process posted data */
        if ($this->_request->isPost()) {
            if ($this->_request->yes) {
                $answerPossibility->delete();
            }
            $this->_redirect('/answer-possibility-group/edit/id/' . $answerPossibilityGroupId);
        }

        /* render view */
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->form = $form;
        $this->_response->setBody($this->view->render('confirm.phtml'));
    }
}