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
 * Tab form for the question text and selection of answer domain (re-use or new)
 *
 * @package    Webenq_Questionnaires_Manage
 * @author     Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_Question_Tab_Group extends WebEnq4_Form
{
    /**
     * Initialises the form
     *
     * @return void
     */
    public function init()
    {
        /* question id for a question in a questionnaire */
        $id = new Zend_Form_Element_Hidden('id');
        $id->removeDecorator('DtDdWrapper');
        $id->removeDecorator('Label');
        $this->addElement($id);

        $qid=new Zend_Form_Element_Hidden('questionnaire_id');
        $qid->removeDecorator('DtDdWrapper');
        $qid->removeDecorator('Label');
        $this->addElement($qid);

        $parentId = new Zend_Form_Element_Hidden('parent_id');
        $parentId->removeDecorator('DtDdWrapper');
        $parentId->removeDecorator('Label');
        $this->addElement($parentId);

        $text = new WebEnq4_Form_Element_MlText('text');
        $text->setAttrib('languages', $this->_languages);
        $text->setAttrib('defaultLanguage',$this->_defaultLanguage);
        $text->setLabel('Text');
        $text->setRequired();
        $this->addElement($text);

/*
        $suggestionsOptions=array();
        //$info['suggestions']=Webenq_Model_QuestionnaireQuestion::getAnswerOptions($questionnaireQuestion->QuestionnaireElement->getTranslation('text'));

        $suggestions=new Zend_Form_Element_Radio('suggestions');
        $suggestions->setLabel('Suggestions');
        $suggestions->addMultiOptions($suggestionsOptions);
        $this->addElement($suggestions);
*/
        $reuse=new Zend_Form_Element_Select('answer_domain_id');
        $reuse->setLabel('Reuse');
        $reuse->addMultiOption("",t('...pick a set of answers options to reuse...'));

        foreach (Webenq_Model_AnswerDomain::getAll() as $answerDomain){
            if ($answerDomain->type=='AnswerDomainChoice'){
                $formOptions[$answerDomain->id]=$answerDomain->getTranslation('name').' ('. t($answerDomain->type).')';
            }
        }
        asort($formOptions);
        $reuse->addMultiOptions($formOptions);
        $this->addElement($reuse);

        $new=new Zend_Form_Element_Select('new');
        $new->setLabel('Add new');
        $new->addMultiOption(0,t('...or add a new set of answer options...'));
        foreach (Webenq_Model_AnswerDomain::getAvailableTypes() as $key=>$value) {
            //todo only for likert
            if ($key=='AnswerDomainChoice'){
                $new->addMultiOption($key,$value['label']);
            }
        }
        $this->addElement($new);

        $cancel = new Zend_Form_Element_Submit('cancel');
        $cancel->setLabel('Cancel');
        $cancel->removeDecorator('DtDdWrapper');
        $this->addElement($cancel);

        $submitQuestionNext=new Zend_Form_Element_Submit('next');
        $submitQuestionNext->setLabel('Next');
        $this->addElement($submitQuestionNext);

        $this->addDisplayGroup(
                array('cancel', 'next'),
                'buttons',
                array('class' => 'table', 'order'=>999)
        );
    }

    public function isValid($value)
    {
        $isValid=parent::isValid($value);

        if ((''==$value['answer_domain_id'] || '0'==$value['answer_domain_id'])
        && (''==$value['new'] || '0'==$value['new'])) {
            $this->new->addError(t('Choose one of these options'));
            return false;
        }

        if (!(''==$value['answer_domain_id'] || '0'==$value['answer_domain_id'])
        && !(''==$value['new'] || '0'==$value['new'])) {
            $this->new->addError(t('Choose only one of these options'));
            return false;
        }

        return $isValid;
    }

}