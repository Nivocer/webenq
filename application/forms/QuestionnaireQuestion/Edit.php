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
 * Form class
 *
 * @package    Webenq_Questionnaires_Manage
 * @author     Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_QuestionnaireQuestion_Edit extends Zend_Form
{
    /**
     * Webenq_Model_QuestionnaireQuestion instance
     *
     * @var Webenq_Model_QuestionnaireQuestion $_questionnaireQuestion
    */
    protected $_info;

    public function __construct(array $info, $options = null)
    {
        $this->_info = $info;
        parent::__construct($options);
    }
    /**
     * Initialises the form
     *
     * @return void
     */

    public function init()
    {
        $info = $this->_info;

        /* question form/tab */
        $questionForm = new Zend_Form_SubForm();
        $id = new Zend_Form_Element_Hidden('id');
        $id->removeDecorator('DtDdWrapper');
        $id->removeDecorator('Label');
        $questionForm->addElement($id);

        $languages = Webenq_Language::getLanguages();
        foreach ($languages as $language) {
            $questionForm->addElement(
                $this->createElement(
                    'text',
                    $language,
                    array(
                        'label' => t('text') . ' (' . $language . '):',
                        'size' => 60,
                        'autocomplete' => 'on',
                        'required' => true,
                        'validators' => array(
                            new Zend_Validate_NotEmpty(),
                        ),
                    )
                )
            );
        }
        $submitQuestionNext=new Zend_Form_Element_Submit('next');
        $submitQuestionNext->setLabel('next');
        $questionForm->addElement($submitQuestionNext);

        $this->addSubForm($questionForm, 'question');

        /* answer options form/tab */
        $answerOptionsForm = new Zend_Form_SubForm();

        $suggestions=new Zend_Form_Element_Radio('suggestions');
        $suggestions->setLabel('Suggestions');
        $suggestions->addMultiOptions($info['suggestions']);
        $answerOptionsForm->addElement($suggestions);

        $reuse=new Zend_Form_Element_Select('reuse');
        $reuse->setLabel('Reuse');
        //$reuse->addMultipleOption(array(0=>t('pick a set of answers options to reuse')));
        $reuse->addMultiOptions(array_merge(array(0=>t('...pick a set of answers options to reuse...')),Webenq_Model_QuestionnaireQuestion::getAnswerOptions()));
        $answerOptionsForm->addElement($reuse);

        $new=new Zend_Form_Element_Select('new');
        $new->setLabel('Add new');
        $new->addMultiOptions(array_merge(array(0=>t('...or add a new set of answer options...')),Webenq_Model_AnswerDomain::getAvailableTypes()));
        $answerOptionsForm->addElement($new);

        $submitAnswerOptionsPrevious=new Zend_Form_Element_Submit('previous');
        $submitAnswerOptionsPrevious->setLabel('previous');
        $answerOptionsForm->addElement($submitAnswerOptionsPrevious);

        $submitAnswerOptionsNext=new Zend_Form_Element_Submit('next');
        $submitAnswerOptionsNext->setLabel('next');
        $answerOptionsForm->addElement($submitAnswerOptionsNext);
        $answerOptionsForm->addDisplayGroup(
            array('previous','next','done'),
            'submitTable',
            array('class' => 'table')
        );
        $this->addSubForm($answerOptionsForm,'answerOptions');


        /* options form/tab */
        //numeric (open: width, slider) choice (radio/checkbox, slider, pulldown)  text (open: num rows, width)
        $optionsForm=new Zend_Form_SubForm();

        //@todo only for choice
        $numberOfAnswers=new Zend_Form_Element_Text('numberOfAnswers');
        $numberOfAnswers->setLabel('How many answers are allowed');
        $optionsForm->addElement($numberOfAnswers);

        $presentation=new Zend_Form_Element_Select('presentation');
        $presentation->setLabel('Presentation');
        $presentation->setMultiOptions($info['presentation']);
        $optionsForm->addElement($presentation);

        //@todo only display for if $presentation=open
        $presentationWidth=new Zend_Form_Element_Select('presentationWith');
        $presentationWidth->setLabel('Width of answer box');
        $presentationWidth->addMultiOptions(Webenq_Model_AnswerDomain::getAnswerBoxWidthOptions());
        $optionsForm->addElement($presentationWidth);

        //@todo only display if $presentation==open && type==text
        $presentationHeight=new Zend_Form_Element_text('presentationWith');
        $presentationHeight->setLabel('Number of rows of answer box');
        $optionsForm->addElement($presentationHeight);

        $required = new Zend_Form_Element_Checkbox('required');
        $required->setLabel('Answer is required');
        $required->getDecorator('Label')->setOption('placement', 'append');
        $optionsForm->addElement($required);
        $optionsForm->addDisplayGroup(
            array('required'),
            'requiredTable',
            array('class' => 'table')
        );

        $active = new Zend_Form_Element_Checkbox('active');
        $active->setLabel('Question is active');
        $active->getDecorator('Label')->setOption('placement', 'append');

        $optionsForm->addElement($active);
        $optionsForm->addDisplayGroup(
            array('active'),
            'activeTable',
            array('class' => 'table')
        );


        $submitOptionsPrevious=new Zend_Form_Element_Submit('previous');
        $submitOptionsPrevious->setLabel('previous');
        $optionsForm->addElement($submitOptionsPrevious);

        $submitOptionsDone=new Zend_Form_Element_Submit('done');
        $submitOptionsDone->setLabel('done');
        $optionsForm->addElement($submitOptionsDone);
        $optionsForm->addDisplayGroup(
            array('previous','next','done'),
            'submitTable',
            array('class' => 'table')
        );

        $this->addSubForm($optionsForm,'options');

    }

    public function isValid($data)
    {
/*        // check if at least one language is filled out
        $hasAtLeastOneLanguage = false;
        foreach ($data['question']['text'] as $language => $translation) {
            if (trim($translation) != '') {
                $hasAtLeastOneLanguage = true;
                break;
            }
        }

        // disable required setting if at least one language was found
        if ($hasAtLeastOneLanguage) {
            foreach ($this->getSubForm('text')->getSubForm('text')->getElements() as $elm) {
                $elm->setRequired(false);
            }
        }
*/
        return parent::isValid($data);
    }

    /**
     * Get the subform name based on the submit button pressed (next/previous/done)
     *
     * assumptions: subforms are in correct order
     *
     * @return boolean|string
     */
    public function getRedirectSubForm (){
        foreach ($this->getSubForms() as $subForm){
            $subForms[]=$subForm->getName();
        }
        foreach ($this->getSubForms() as $subForm){
            $key=array_search($subForm->getName(), $subForms);
            if (isset($subForm->previous) && $subForm->previous->isChecked()){
                if ($key>0){
                    return $subForms[$key-1];
                }else {
                    return false;
                }
            } elseif  (isset($subForm->next) && $subForm->next->isChecked()){
                if ($key<count($subForms)-1){
                    return $subForms[$key+1];
                }else {
                    return 'done';
                }
            } elseif  (isset($subForm->done) && $subForm->done->isChecked()){
                return 'done';
            }
        }
        return false;
    }

 public function isCancelled($values)
    {
        return (isset($values['cancel']));
    }
}