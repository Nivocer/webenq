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
class QuestionnaireQuestionController extends Zend_Controller_Action
{
    /**
     * Controller actions that are ajaxable
     *
     * @var array
     */
    public $ajaxable = array(
       // 'add' => array('html'),
        'edit' => array('html'),
        //'delete' => array('html'),
        'add-subquestion' => array('html'),
    );

    public $questionnaireQuestion;

    /**
     * Renders the form for adding an existing question to a questionnaire
     */
    public function addAction()
    {
        //questionnaire id from url
        if (isset ($this->_request->questionnaire_id)){
            $questionnaireId = $this->_request->questionnaire_id;
        }

        //questionnaire id from post data
        $postData=$this->getRequest()->getPost();
        if (isset($postData['questionnaire_id'])){
            $questionnaireId=$postData['questionnaire_id'];
        }
        if (!$questionnaireId) {
            throw new Exception('No questionnaire id given!');
        }

        //valid questionnaireId?
        $questionnaireModel=new Webenq_Model_Questionnaire();
        $questionnaire=$questionnaireModel->getTable()->findById($questionnaireId);
        if ($questionnaire->count()==0){
            throw new Exception(sprintf('Invalid questionnaire id given: %s', $questionnaireId));
        }

        if (isset($postData['answers']['type']) && $postData['answers']['type']) {
            $answerDomainType=$postData['answers']['type'];
        } else {
            $answerDomainType='AnswerDomainNumeric';
        }
        $form='Webenq_Form_QuestionnaireNode_Properties_QuestionNode';
        $this->view->form = new $form(
            array(
                'answerDomainType' => $answerDomainType,
                'defaultLanguage'=>$questionnaire->getFirst()->default_language,
            )
        );


        $this->view->form->setAction($this->view->baseUrl('/questionnaire-question/add'));
        if ($this->getRequest()->isPost()){
            if ($this->_helper->form->isCancelled($this->view->form)) {
                $redirectUrl = 'questionnaire/edit/id/' . $questionnaireId;
                $this->_redirect($redirectUrl);
                return;
            }else{
                //fill information from forms
                $this->view->form->setDefaults($this->getRequest()->getPost());
                $formData=$this->view->form->getValues();
                $submitInfo=$this->view->form->getSubmitButtonUsed();
                if ($this->view->form->getSubForm($submitInfo['subForm'])->isValid($this->getRequest()->getPost())){
                    //get action stack from controller to perform based on the form data
                    $situations=$this->view->form->getSituations($formData);
                    $this->actOnSituation($situations, $formData);
                    //redirect to other tab (or preview questionnaire when done)
                    $this->redirectTo($submitInfo, true);
                }else {
                    //subform is not valid: go to current tab
                    $this->view->activeTab=$submitInfo['subForm'];
                }
            }
        }else{

            $this->questionnaireQuestion=new Webenq_Model_QuestionnaireNode();
            $this->questionnaireQuestion->Questionnaire=$questionnaire;

            $this->view->form->setDefaults($this->questionnaireQuestion->toArray());
            if (isset($this->_request->parent_id)){
                $this->view->form->setDefaults(array('parent_id'=>$this->_request->parent_id));
            }
        }
    }

    /**
     * Edit a question
     *
     * Required: an id of a questionnaire node.
     *
     * The questionnaire node id can be used to find both the questionnaire (the
     * root of the tree in which the node resides) and the associated question.
     *
     * @todo clean function (splits?)
     * @return void
     */
    public function editAction()
    {
        $questionnaireNode = Doctrine_Core::getTable('Webenq_Model_QuestionnaireNode')
        ->find($this->_request->id);

        if (!$questionnaireNode) {
            $this->_redirect('/questionnaire/');
            return;
        }
        //hack lazy loading?????
        $questionnaireNode->QuestionnaireElement->AnswerDomain->AnswerDomainItem;

        $questionnaire = Doctrine_Core::getTable('Webenq_Model_Questionnaire')
        ->findBy('questionnaire_node_id', $questionnaireNode->root_id)
        ->getFirst();

        $formClassName = 'Webenq_Form_QuestionnaireNode_Properties_'.substr($questionnaireNode->type, 13);
        $this->view->form = new $formClassName(
            array(
                'defaultLanguage' => $questionnaire->default_language,
            )
        );
        $this->view->form->setAction($this->view->baseUrl($this->_request->getPathInfo()));

        if ($this->getRequest()->isPost()) {
            $this->view->form->adapt($this->getRequest()->getPost());

            if ($this->_helper->form->isCancelled($this->view->form)) {
                $redirectUrl = 'questionnaire/edit/id/' . $questionnaire->id;
                $this->_redirect($redirectUrl);
                return;
            } else {
                if ($this->view->form->isValid($this->getRequest()->getPost())) {
                    $questionnaireNode->fromArray($this->view->form->getValues());
                    $questionnaireNode = $this->actOnSituations($questionnaireNode, $this->view->form->situations, $this->view->form->getValues());
                    if (in_array('doneButtonPressed', $this->view->form->situations)) {
                        //$questionnaireNode->save();
                        $questionnaireNode->QuestionnaireElement->AnswerDomain->save();
                        $redirectUrl = 'questionnaire/edit/id/' . $questionnaire->id;
                        $this->_redirect($redirectUrl);
                        return;
                    }
                }else {
                    $questionnaireNode->fromArray($this->view->form->getValues());
                    $questionnaireNode = $this->actOnSituations($questionnaireNode, $this->view->form->situations, $this->view->form->getValues());
                }
            }
        }

        $this->view->form->adapt($questionnaireNode->toArray());
        $this->view->form->setDefaults($questionnaireNode->toArray());
    }

    public function actOnSituations($questionnaireNode, $situations, $postData)
    {
        //@todo check to see if there is a php/zend-function for it like _forward (__call)?
        //@todo reset   Webenq_Form_AnswerDomain_Items() private $_itemsAdded ?
        foreach ($situations as $situation){
            switch ($situation){
                case 'differentAnswerDomainChosen':
                    //get answerdomain from database, keep active/required from postdata
                    $answerDomainModel=new Webenq_Model_AnswerDomain();
                    if (isset($questionnaireNode->QuestionnaireElement->answer_domain_id)) {
                        $answerDomain=$answerDomainModel->getTable()->find($questionnaireNode->QuestionnaireElement->answer_domain_id);
                    }
                    $questionnaireNode->QuestionnaireElement->AnswerDomain=$answerDomain;
                    break;
                case 'newAnswerDomainChosen':
                    //clear answers and options tab, only keep required and active
                    $answerDomain=new Webenq_Model_AnswerDomain();
                    $answerDomain->type=$postData['question']['new'];
                    $questionnaireNode->QuestionnaireElement->AnswerDomain=$answerDomain;
                    break;

                case 'newAnswerDomainTypeChosen':
                    //other answers/options subform keep as much info from postdata as possible
                    $answerDomain=new Webenq_Model_AnswerDomain();
                    $answerDomain->type=$postData['question']['new'];
                    $answerDomain->fromArray($postData['answer']);
                    $questionnaireNode->QuestionnaireElement->AnswerDomain=$answerDomain;
                    break;
                case 'newAnswerDomainSameTypeChosen':
                    $answerDomain=new Webenq_Model_AnswerDomain();
                    $answerDomain->type=$postData['question']['new'];
                    $answerDomain->fromArray($postData['answer']);
                    $questionnaireNode->QuestionnaireElement->AnswerDomain=$answerDomain;
                    break;
                case 'newAndExistingAnswerDomainChoosen':
                    //@todo what should we do:
                    //$form->adapt is doing $this->_answerDomainType=$data['answer']['type'];
                    break;
            }
        }
        return $questionnaireNode;
    }

    /**
     * redirect user to correct location, if soft redirect, we want to redirect to another tab
     *
     * @param array $submitInfo which submitbutton on which tab is pushed
     * @param boolean $soft when true set active tab, else redirect to other page
     * @deprecated
     */
    public function redirectTo($submitInfo, $soft) {
        //build redirect url
        $redirectSubForm=$this->view->form->getRedirectSubform($submitInfo);
        if ($redirectSubForm=='done') {
            //do we have info from database
            if ($this->questionnaireQuestion && $this->questionnaire) {
                $questionnaireId=$this->questionnaire->id;
                $pageId=$this->questionnaireQuestion->getPage()->id;
            } else {
                //info from form
                $questionnaireId=$this->view->form->question->getValue('questionnaire_id');
                //we have a parent_id, in form, it is page id...
                if ($this->view->form->question->getValue('parent_id')) {
                    $pageId=$this->view->form->question->getValue('parent_id');
                } else {
                    //we don't have a parent_id, question should be added to last page, so redirect to it
                    $questionnaireModel=new Webenq_Model_Questionnaire();
                    $questionnaire=$questionnaireModel->getQuestionnaire($questionnaireId);
                    $pageId=$questionnaire->getLastPage()->id;

                }
            }

            $redirectUrl = 'questionnaire/edit/id/' . $questionnaireId.'/#pageId-'.$pageId;
            //if ((int) $this->questionnaireQuestion->CollectionPresentation[0]->page !== 0) {
            //    $redirectUrl .= '#page-' . $this->questionnaireQuestion->CollectionPresentation[0]->page;
            //}
        } else {
            if ($soft) {
                $this->view->activeTab=$redirectSubForm;
                return;
            } else {
                //when we want to get info from database (reset?)
                //note: if redirectSubform ==false -> go to first tab
                $redirectUrl = 'questionnaire-question/edit/id/' . $this->questionnaireQuestion->id;
                $redirectUrl .= '#' . $redirectSubForm;
            }
        }

        // close dialog and redirect
        if ($this->_request->isXmlHttpRequest()) {
            $this->_helper->json(
                array(
                    'reload' => true,
                    'href' => $this->view->baseUrl($redirectUrl),
                )
            );
        } else {
            $this->_redirect($redirectUrl);
            return;
        }
    }

    public function saveQuestionnaireQuestion()
    {
        $this->questionnaireQuestion->save();
        $this->_helper->getHelper('FlashMessenger')
        ->setNamespace('success')
        ->addMessage(
            sprintf(
                t('Question "%s" updated succesfully'),
                //@todo check questiontext
                $this->questionnaireQuestion->QuestionnaireElement->getTranslation('text')
            )
        );
    }

    /**
     * Renders the form for deleting a question from a questionnaire,
     * or completely deleting it from the repository.
     * @todo redirect to correct page-tab
     * @return void
     */
    public function deleteAction()
    {
        $questionnaireNode=new Webenq_Model_QuestionnaireNode();
        $this->questionnaireQuestion=$questionnaireNode->getTable()->find($this->_request->id);

        if (!$this->questionnaireQuestion){
            $this->_redirect('/questionnaire/');
            return;
        }

        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            //get questionnaire before we eventually delete questionnaire question
            $questionnaire=$this->questionnaireQuestion->getQuestionnaire();
            if (isset($data['yes'])) {
                $this->questionnaireQuestion->delete();
                $this->_helper->getHelper('FlashMessenger')
                    ->setNamespace('success')
                    ->addMessage(sprintf('Question `%s` deleted',
                        $this->questionnaireQuestion->QuestionnaireElement->getTranslation('text')
                        ));
                if ($this->_request->isXmlHttpRequest()) {
                    $this->_helper->json(array('reload' => true));
                } else {
                    $this->_redirect('/questionnaire/edit/id/'.$questionnaire->id);
                    return;
                }
            } else {
                $this->_helper->getHelper('FlashMessenger')
                    ->setNamespace('success')
                    ->addMessage(sprintf('Question `%s` NOT deleted',
                         $this->questionnaireQuestion->QuestionnaireElement->getTranslation('text')
                        ));
                if ($this->_request->isXmlHttpRequest()) {
                    $this->_helper->json(array('reload' => false));
                } else {
                    $this->_redirect('/questionnaire/edit/id/'.$questionnaire->id);
                    return;
                }
            }

        }
        $confirmationText = sprintf(
            t('Are you sure you want to remove the question `%s` (and answers) from the current questionniare? It is also possible to hide a question'),
            $this->questionnaireQuestion->QuestionnaireElement->getTranslation('text')
            );
        $this->view->form = new Webenq_Form_Confirm($this->questionnaireQuestion->id, $confirmationText);
        $this->view->form->setAction($this->view->baseUrl('/questionnaire-question/delete/id/' . $this->_request->id));
    }


}
