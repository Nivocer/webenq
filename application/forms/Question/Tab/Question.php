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
class Webenq_Form_Question_Tab_Question extends Webenq_Form_Question_Tab
{
    /**
     * Initialises the form
     *
     * @return void
     */
    public function init()
    {
        $id = new Zend_Form_Element_Hidden('id');
        $id->removeDecorator('DtDdWrapper');
        $id->removeDecorator('Label');
        $id->setBelongsTo('question');
        $this->addElement($id);

        $text = new WebEnq4_Form_Element_MlText('text');
        $text->setAttrib('languages', $this->_languages);
        $text->setAttrib('defaultLanguage',$this->_defaultLanguage);
        $text->setLabel('Text');
        $text->setBelongsTo('question');
        $text->setRequired();
        $this->addElement($text);

        /*$suggestionsOptions=array();
        //$info['suggestions']=Webenq_Model_QuestionnaireQuestion::getAnswerOptions($questionnaireQuestion->QuestionnaireElement->getTranslation('text'));

        $suggestions=new Zend_Form_Element_Radio('suggestions');
        $suggestions->setLabel('Suggestions');
        $suggestions->addMultiOptions($suggestionsOptions);
        $this->addElement($suggestions);
*/
        $reuse=new Zend_Form_Element_Select('answer_domain_id');
        $reuse->setLabel('Reuse');
        $reuse->setBelongsTo('question');
        $reuse->addMultiOption("",t('...pick a set of answers options to reuse...'));

        foreach (Webenq_Model_AnswerDomain::getAll() as $answerDomain){
            $formOptions[$answerDomain->id]=$answerDomain->getTranslation('name').' ('. t($answerDomain->type).')';
        }
        asort($formOptions);
        $reuse->addMultiOptions($formOptions);
        $this->addElement($reuse);

        $new=new Zend_Form_Element_Select('new');
        $new->setLabel('Add new');
        $new->setBelongsTo('question');
        $new->addMultiOption(0,t('...or add a new set of answer options...'));
        foreach (Webenq_Model_AnswerDomain::getAvailableTypes() as $key=>$value) {
            $new->addMultiOption($key,$value['label']);
        }
        $this->addElement($new);

        $cancel = new Zend_Form_Element_Submit('cancel');
        $cancel->setLabel('Cancel');
        $cancel->removeDecorator('DtDdWrapper');
        $this->addElement($cancel);

        $submitQuestionNext=new Zend_Form_Element_Submit('next');
        $submitQuestionNext->setLabel('Next');
        $submitQuestionNext->setBelongsTo('question');
        $this->addElement($submitQuestionNext);

        $this->addDisplayGroup(
                array('cancel', 'next'),
                'buttons',
                array('class' => 'table', 'order'=>999)
        );
    }

}