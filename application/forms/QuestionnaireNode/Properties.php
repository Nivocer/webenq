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
 * Form to deal with question properties (text, answers, options).
 *
 * @package    Webenq_Questionnaires_Manage
 * @author     Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_QuestionnaireNode_Properties extends WebEnq4_Form
{
    /**
     * Type of answer domain: text, numeric, choice
     *
     * @var string
     */
    public $activeTab;
    public $_answerDomainType;
    public $situations;
    public $_subFormNames;
    public $_defaultLanguage;
    public $_submitInfo;

    private $_answerTypeSpecificForms=array(
        'Webenq_Form_AnswerDomain_Tab',
        'Webenq_Form_QuestionnaireNode_Tab_Options'
    );

    /**
     * Initialises the form, sets the answer domain type
     *
     * @param mixed $options
     * @return void
     */
    public function __construct($options = null)
    {
        if (is_array($options) && isset($options['defaultLanguage'])) {
            $this->_defaultLanguage=$options['defaultLanguage'];
        }
        parent::__construct();
    }

    public function init()
    {
        $qid=new Zend_Form_Element_Hidden('questionnaire_id');
        $qid->removeDecorator('DtDdWrapper');
        $qid->removeDecorator('Label');
        $this->addElement($qid);

        $parentId = new Zend_Form_Element_Hidden('parent_id');
        $parentId->removeDecorator('DtDdWrapper');
        $parentId->removeDecorator('Label');
        $this->addElement($parentId);

        foreach ($this->_subFormNames as $subForm) {
            if ($this->getSubForm($subForm)) {
                $this->removeSubForm($subForm);
            }
            $this->initSubFormAsTab($subForm);
        }
    }

    public function adapt(array $data) {

    }
    public function _initDetermineFormName($tabName){
        switch ($tabName){
            case 'answer':
                $formName='Webenq_Form_AnswerDomain_Tab';
                break;
            default:
                $formName='Webenq_Form_QuestionnaireNode_Tab_'.ucfirst($tabName);
                break;
        }
        //add answerdomain specific extension if neccessary
        if (in_array($formName, $this->_answerTypeSpecificForms)){
            if (in_array($this->_answerDomainType, array('AnswerDomainChoice', 'AnswerDomainNumeric', 'AnswerDomainText'))) {
                $formName.='_'.substr($this->_answerDomainType, 12);
            }
        }
    return $formName;
    }

    /**
     * Set defaults for question properties form
     *
     * The provided $defaults should be similar to the output of toArray() on
     * a questionnaire node.
     *
     * <ul>
     * <li>['id'], ['type'], ['root_id'], ...: node attributes
     * <li>['QuestionnaireElement']: related questionnaire question element
     * <li>['QuestionnaireElement']['AnswerDomain']: answer domain related to the questionnaire question element
     * </ul>
     *
     * If no ['QuestionnaireElement'] sub array is available, existing values
     * for ['question'], ['answers'] and ['options'] will be preserved.
     *
     * @param array Array with data for a questionnaire node
     */
    public function setDefaults(array $defaults)
    {
        parent::setDefaults($defaults);
    }

    public function isValid($data)
    {
        $this->_submitInfo=$this->getSubmitButtonUsed($data);

        if (in_array($this->_submitInfo['name'], array('next', 'previous') )) {
            $isValid= $this->getSubForm($this->_submitInfo['subForm'])->isValid($data[$this->_submitInfo['subForm']]);
        }
        //process all forms, but don't use it to determin if current submission is valid
        parent::isValidPartial($data);

        return $isValid;
    }

    /**
     * Analyses the form values to determine the situations that could occur:
     *
     * <ul>
     * <li>The question tab has been submitted
     * </ul>
     *
     * @return array: array with situations that needs action before redisplay form
     */
    public function getSituations(array $data)
    {
        return $this->situations;
    }
}
