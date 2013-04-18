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
        'delete' => array('html'),
        'add-subquestion' => array('html'),
    );

    public $questionnaireQuestion;
    /**
     * Renders the form for adding an existing question to a questionnaire
     */
    public function addAction()
    {
        if (isset ($this->_request->questionnaire_id)){
        $questionnaireId = $this->_request->questionnaire_id;
        }
        $formData=$this->getRequest()->getPost();
        if (isset($formData['questionnaire_id'])){
            $questionnaireId=$formdata['questionnaire_id'];
        }
        //@todo remove hardcoded questionnaireId adjust form
        $questionnaireId=1;
        if (!$questionnaireId) {
            throw new Exception('No questionnaire id given!');
        }

        $questionnaireModel=new Webenq_Model_Questionnaire();
        $questionnaire=$questionnaireModel->getTable()->findById($questionnaireId);
        $this->questionnaireQuestion=new Webenq_Model_QuestionnaireNode();
        $this->questionnaireQuestion->Questionnaire=$questionnaire;

        $form = new Webenq_Form_Question_Properties(array('answerDomainType' => 'numeric', 'defaultLanguage'=>$questionnaire->getFirst()->default_language));

        $form->setAction($this->view->baseUrl('/questionnaire-question/add'));
        if ($this->getRequest()->isPost()){
            //is form cancelled-> redirect to preview questionnaire
            if ($this->_helper->form->isCancelled($form)) {
                $redirectUrl = 'questionnaire/edit/id/' . $questionnaireId;
                $this->_redirect($redirectUrl);
                return;
            }else{
                //which subform is posted
                $formData=$this->getRequest()->getPost();
                $submitInfo=$this->_helper->form->getSubmitButtonUsed($formData, array('next', 'previous','done'));
                //is subform valid
                if ($form->getSubForm($submitInfo['subForm'])->isValid($formData)){
                    //fill information from all subforms
                    $form->isValid($formData);
                    //perform subform action
                    $newValues = $form->getValues();

                    $this->questionnaireQuestion->fromArray($newValues);
                    switch ($submitInfo['subForm']){
                    case 'question':
                        $form=$this->questionSubForm($form, $newValues);
                        break;
                    case 'answers':
                        $form=$this->answersSubForm($form, $newValues);
                        break;
                    case 'options':
                        $form=$this->optionsSubForm($form,$newValues);
                        if ($submitInfo['name']=='done'){
                            $this->saveQuestionnaireQuestion($this->questionnaireQuestion);
                        }
                        break;
                    }
                    //redirect to other tab (or preview questionnaire when done)
                    $this->redirectTo($form, $submitInfo, true);
                }
            }
        }
        //display form
        //info needed for form
        $form->setDefaults($this->questionnaireQuestion->toArray());
        $this->view->form = $form;
        $this->view->questionnaireQuestion = $this->questionnaireQuestion;


    }

    /**
     * Renders the form for editing a questionnaire and handle saving of form.
     * @todo clean function (splits?)
     * @return void
     */
    public function editAction()
    {
        $questionnaireNode=new Webenq_Model_QuestionnaireNode();
        $this->questionnaireQuestion=$questionnaireNode->getTable()->find($this->_request->id);
        $questionnaire=new Webenq_Model_Questionnaire();
        //fix add questionnaire to questionnaireQuestion, maybe relation problem?
        $this->questionnaireQuestion->Questionnaire=$questionnaire->getTable()->findBy('questionnaire_node_id', $this->questionnaireQuestion->root_id);

        if (!$this->questionnaireQuestion){
            $this->_redirect('/questionnaire/');
            return;
        }
        // get form
        $answerDomainType=$this->questionnaireQuestion->QuestionnaireElement->AnswerDomain->type;
        $this->view->form = new Webenq_Form_Question_Properties(array('answerDomainType' => $answerDomainType, 'defaultLanguage'=>$this->questionnaireQuestion->Questionnaire->getFirst()->default_language));
        $this->view->form->setAction($this->view->baseUrl($this->_request->getPathInfo()));

        //is posted?/process form
        if ($this->getRequest()->isPost()){
            //is form cancelled-> redirect to preview questionnaire
            if ($this->_helper->form->isCancelled($this->view->form)) {
                $redirectUrl = 'questionnaire/edit/id/' . $this->questionnaireQuestion->Questionnaire->getFirst()->id;
                $this->_redirect($redirectUrl);
                return;
            }else{
                //fill information from forms
                $this->view->form->setDefaults($this->getRequest()->getPost());
                $postData=$this->view->form->getValues();
                if (isset($postData['question']['question']['id']) && $postData['question']['question']['id']==$this->questionnaireQuestion->get('id')) {
                    $submitInfo=$this->view->form->getSubmitButtonUsed();
                    if ($this->view->form->getSubForm($submitInfo['subForm'])->isValid($this->getRequest()->getPost())){
                        //get action stack from controller to perform based on the form data
                        $situations=$this->view->form->getSituations();
                        $this->actOnSituation($situations, $postData);
                        //redirect to other tab (or preview questionnaire when done)
                        $this->redirectTo($submitInfo, true);
                    }else {
                        //subform is not valid: go to current tab
                        //@todo need work?
                    }
                } else {
                    $this->_helper->getHelper('FlashMessenger')
                    ->setNamespace('error')
                    ->addMessage(
                        t('Question identifier mismatch, something went wrong')
                    );
                }

            }
        } else {
            //data from database
            $this->view->form->setDefaults($this->questionnaireQuestion->toArray());
        }
        //@todo adjust form so we don't need $questionnaireQuestion in form
        //add questionnaire info to form, we need the question text, but we could get it from form-data
        $this->view->questionnaireQuestion = $this->questionnaireQuestion;
    }

    public function actOnSituation ($situations, $postdata){
        //@todo check to see if there is a php/zend-function for it like _forward (__call)?
        foreach ($situations as $situation){
            switch ($situation){
                case 'differentAnswerDomainChosen':
                    //get answerdomain from database, keep active/required from postdata
                    $answerDomainModel=new Webenq_Model_AnswerDomain();
                    $answerDomain=$answerDomainModel->getTable()->find($postData['question']['question']['answer_domain_id']);
                    $this->view->form->answerDomainType=$answerDomain->type;
                    $this->view->form->initDeterminClasses();
                    $this->view->form->initAnswersTab();
                    $this->view->form->getSubform('answers')->setDefaults($answerDomain->toArray());
                    $this->view->form->initOptionsTab();
                    $this->view->form->getSubform('options')->setDefaults($answerDomain->toArray());
                    $temp['required']=$postData['options']['options']['required'];
                    $temp['active'] =$postData['options']['options']['active'];
                    $this->view->form->getSubform('options')->setDefaults($temp);
                    break;
                case 'newAnswerDomainChosen':
                    //clear answers and options tab, only keep required and active
                    $this->view->form->answerDomainType='AnswerDomain'.$postData['question']['question']['new'];
                    $this->view->form->initDeterminClasses();
                    $this->view->form->initAnswersTab();
                    $this->view->form->initOptionsTab();
                    $temp['required']=$postData['options']['options']['required'];
                    $temp['active'] =$postData['options']['options']['active'];
                    $this->view->form->getSubform('options')->setDefaults($temp);
                    break;
                case 'newAnswerDomainTypeChosen':
                    //other answers/options subform keep as much info from postdata as possible
                    $this->view->form->answerDomainType='AnswerDomain'.$postData['question']['question']['new'];
                    $this->view->form->initDeterminClasses();
                    $this->view->form->initAnswersTab();
                    $this->view->form->initOptionsTab();
                    $this->view->form->setDefaults($postData);
                    break;
                case 'newAnswerDomainSameTypeChosen':
                    //no action needed
                    break;
            }
        }
    }

    /**
     * redirect user to correct location, if soft redirect, we want to redirect to another tab
     *
     * @param unknown $form
     * @param unknown $submitInfo which submitbutton on which tab is pushed
     * @param unknown $soft
     */
    public function redirectTo($submitInfo, $soft) {
        //build redirect url
        //@todo check redirecturl
        $redirectSubForm=$this->view->form->getRedirectSubform($submitInfo);

        //$formIdTranslation=array('question'=>'questions', 'answerOptions'=>'answerOptions', 'options'=>'options');
        if ($redirectSubForm=='done'){
            $redirectUrl = 'questionnaire/edit/id/' . $this->questionnaireQuestion->Questionnaire->getFirst()->id;
            //if ((int) $this->questionnaireQuestion->CollectionPresentation[0]->page !== 0) {
            //    $redirectUrl .= '#page-' . $this->questionnaireQuestion->CollectionPresentation[0]->page;
            //}
        }else {
            if  ($soft){
                $this->view->activeTab=$redirectSubForm;
                return;
            }else {
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
    public function saveQuestionnaireQuestion(){
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
     *
     * @return void
     */
    public function deleteAction()
    {
        $questionnaireQuestion = Doctrine_Query::create()
            ->from('Webenq_Model_QuestionnaireQuestion qq')
            ->innerJoin('qq.Question q WITH qq.id = ?', $this->_request->id)
            ->leftJoin('q.QuestionText qt')
            ->where('qt.language = ?', $this->_helper->language())
            ->execute()
            ->getFirst();

        $form = new Webenq_Form_QuestionnaireQuestion_Delete($questionnaireQuestion);
        $form->setAction($this->view->baseUrl($this->_request->getPathInfo()));

        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();
            if (isset($data['yes'])) {
                $questionnaireQuestion->delete();
                if ($this->_request->isXmlHttpRequest()) {
                    $this->_helper->json(array('reload' => true));
                }
            } else {
                if ($this->_request->isXmlHttpRequest()) {
                    $this->_helper->json(array('reload' => false));
                }
            }
        }

        $this->view->form = $form;
        $this->view->questionnaireQuestion = $questionnaireQuestion;
    }

    protected function _getSubQuestions(Webenq_Model_QuestionnaireQuestion $questionnaireQuestion)
    {
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
    public function saveStateAction()
    {
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
    protected function _saveGridSubquestions($parentId, array $grid)
    {
        /* get collection-presentation object for given parent */
        $cp = Doctrine_Core::getTable(
            'Webenq_Model_QuestionnaireQuestion'
        )->find($parentId)->CollectionPresentation->getFirst();
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
                $current = Doctrine_Core::getTable('Webenq_Model_QuestionnaireQuestion')
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

    public function addSubquestionAction()
    {
        $qq = Doctrine_Query::create()
            ->from('Webenq_Model_QuestionnaireQuestion qq')
            ->innerJoin('qq.CollectionPresentation cp')
            ->where('qq.id != ?', $this->_request->id)
            ->andWhere('cp.parent_id IS NULL')
            ->execute();
        $this->view->qq = $qq;
    }


}