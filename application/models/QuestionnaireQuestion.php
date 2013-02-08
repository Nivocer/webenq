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
 * @package    Webenq_Models
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Questionnaire class definition
 *
 * @package    Webenq_Models
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Model_QuestionnaireQuestion extends Webenq_Model_Base_QuestionnaireQuestion
{
    /**
     * The question text in the current language
     *
     * @var string
     */
    protected $_questionText;


    public function getAnswer(Webenq_Model_Respondent $respondent)
    {
        $answers = Doctrine_Query::create()
        ->from('Webenq_Model_Answer a')
        ->where('a.respondent_id = ?', $respondent->id)
        ->andWhere('a.questionnaire_question_id = ?', $this->id)
        ->limit(1)
        ->execute();

        if (count($answers) === 1) {
            return $answers->getFirst();
        }

        return false;
    }

    /**
     * Returns a xform element or nested group of xform elements, to be used
     * in the html body of a html/xform document
     *
     * @param DOMDocument $xml
     * @param DOMElement $group
     * @return DOMElement
     */
    public function getXformElement(DOMDocument $xml, DOMElement $group = null)
    {
        if (!is_null($this->answerPossibilityGroup_id)) {
            $element = $xml->createElement('select1');
            $element->setAttribute('ref', $this->getXpath());
            $label = $xml->createElement('label', Webenq::Xmlify($this->Question->getQuestionText()->text));
            $element->appendChild($label);
            foreach ($this->AnswerPossibilityGroup->getAnswerPossibilities() as $ap) {
                $item = $xml->createElement('item');
                $label = $xml->createElement('label', Webenq::Xmlify($ap->getAnswerPossibilityText()->text));
                $value = $xml->createElement('value', ($ap->value ? $ap->value : $ap->id));
                $item->appendChild($label);
                $item->appendChild($value);
                $element->appendChild($item);
            }
        } else {
            $element = $xml->createElement('input');
            $element->setAttribute('ref', $this->getXpath());
            $label = $xml->createElement('label', Webenq::Xmlify($this->Question->getQuestionText()->text));
            $element->appendChild($label);
        }

        $subQqs = Webenq_Model_QuestionnaireQuestion::getSubQuestions($this);
        if ($subQqs->count() > 0) {
            $originalElm = $element;
            $element = $xml->createElement('group');
            $element->setAttribute('ref', $this->getXpath());
            foreach ($originalElm->childNodes as $item) {
                $element->appendChild($item);
            }

            foreach ($subQqs as $subQq) {
                $subElm = $subQq->getXformElement($xml, $element);
                $element->appendChild($subElm);
            }
        }
        return $element;
    }

    /**
     * Returns a xform instance element or nested group of xform instance
     * elements, to be used in the html head of a html/xform document
     *
     * @param DOMDocument $xml
     * @param DOMElement $group
     * @return DOMElement
     */
    public function getXformInstanceElement(DOMDocument $xml, DOMElement $group = null)
    {
        $element = $xml->createElement(Webenq::Xmlify('q' . $this->id, 'tag'));
        $subQqs = Webenq_Model_QuestionnaireQuestion::getSubQuestions($this);
        if ($subQqs->count() > 0) {
            foreach ($subQqs as $subQq) {
                $subElm = $subQq->getXformInstanceElement($xml, $element);
                $element->appendChild($subElm);
            }
        }
        return $element;
    }

    /**
     * Returns a xform bind element, to be used in the html head of a
     * html/xform document
     *
     * @param DOMDocument $xml
     * @param array $elements
     * @return DOMElement
     */
    public function getXformBindElements(DOMDocument $xml, array $elements = array())
    {
        if (!is_null($this->answerPossibilityGroup_id)) {
            $element = $xml->createElement('bind');
            $element->setAttribute('nodeset', $this->getXpath());
            $element->setAttribute('type', 'select1');
        } else {
            $element = $xml->createElement('bind');
            $element->setAttribute('nodeset', $this->getXpath());
            $element->setAttribute('type', 'string');
        }
        $elements[] = $element;

        $subQqs = Webenq_Model_QuestionnaireQuestion::getSubQuestions($this);
        if ($subQqs->count() > 0) {
            $element->setAttribute('readonly', 'true()');
            foreach ($subQqs as $subQq) {
                $elements = array_merge($subQq->getXformBindElements($xml, $elements), $elements);
            }
        }
        return $elements;
    }

    public function getXformData(Webenq_Model_Respondent $respondent, DOMDocument $xml, DOMElement $group = null)
    {
        // add element for current question
        $element = $xml->createElement(Webenq::Xmlify("q$this->id", 'tag'));

        // add answer, if any
        $answer = $this->getAnswer($respondent);
        if ($answer) {
            if ($answer->answerPossibility_id) {
                $element->nodeValue = $answer->AnswerPossibility->value;
                if (!$element->nodeValue) $element->nodeValue = $answer->answerPossibility_id;
            } else {
               $element->nodeValue = Webenq::Xmlify($answer->text, 'value');
            }
        }

        // add subquestions
        $subQqs = Webenq_Model_QuestionnaireQuestion::getSubQuestions($this);
        if ($subQqs->count() > 0) {
            foreach ($subQqs as $subQq) {
                $subElm = $subQq->getXformData($respondent, $xml, $element);
                $element->appendChild($subElm);
            }
        }

        return $element;
    }

    /**
     * Returns an instance of Zend_Form_SubForm if it has sub-questions,
     * and an instance of Zend_Form_Element otherwise.
     *
     * @param Webenq_Model_Respondent $respondent (optional) If provided the form element is filled
     *   with the respondent's answer
     * @return Zend_Form_SubForm|Zend_Form_Element
     */
    public function getFormElement(Webenq_Model_Respondent $respondent = null)
    {
        $name = "qq_$this->id";
        $subQuestions = self::getSubQuestions($this);

        if ($subQuestions->count() > 0) {

            // create subform
            $subForm = new Zend_Form_SubForm();
            $subForm->setLegend($this->Question->getQuestionText()->text)
            ->removeDecorator('DtDdWrapper');

            // add child questions to subform
            foreach ($subQuestions as $subQuestion) {
                $name = "qq_$subQuestion->id";
                $element = $subQuestion->getFormElement($respondent);
                if ($element instanceof Zend_Form_Element) {
                    $subForm->addElement($element, $name);
                } else {
                    $subForm->addSubForm($element, $name);
                }
            }
            return $subForm;

        } else {

            // set default element type if not yet set
            if (!$this->CollectionPresentation[0]->type) {
                $this->CollectionPresentation[0]->setDefaults($this);
                $this->CollectionPresentation[0]->save();
            }

            // instantiate form element
            switch ($this->CollectionPresentation[0]->type) {
                case Webenq::COLLECTION_PRESENTATION_TEXT:
                    $element = new WebEnq4_Form_Element_Note($name);
                case Webenq::COLLECTION_PRESENTATION_OPEN_TEXT:
                    $element = new Zend_Form_Element_Text($name);
                    break;
                case Webenq::COLLECTION_PRESENTATION_OPEN_TEXTAREA:
                    $element = new Zend_Form_Element_Textarea($name);
                    $element ->setAttrib('cols', '80');
                    $element ->setAttrib('rows', '4');
                    break;
                case Webenq::COLLECTION_PRESENTATION_OPEN_DATE:
                    $element = new ZendX_JQuery_Form_Element_DatePicker($name);
                    $element->addFilter(new Webenq_Filter_Date());
                    break;
                case Webenq::COLLECTION_PRESENTATION_OPEN_CURRENTDATE:
                    $element = new Webenq_Form_Element_CurrentDate($name);
                    $element->removeDecorator('Label');
                    break;
                case Webenq::COLLECTION_PRESENTATION_SINGLESELECT_RADIOBUTTONS:
                    $element = new Zend_Form_Element_Radio($name);
                    break;
                case Webenq::COLLECTION_PRESENTATION_SINGLESELECT_DROPDOWNLIST:
                    $element = new Zend_Form_Element_Select($name);
                    break;
                case Webenq::COLLECTION_PRESENTATION_SINGLESELECT_SLIDER:
                    $element = new ZendX_JQuery_Form_Element_Slider($name);
                    $element->setJQueryParams(array('value' => '50'));
                    break;
                case Webenq::COLLECTION_PRESENTATION_MULTIPLESELECT_CHECKBOXES:
                    $element = new Zend_Form_Element_MultiCheckbox($name);
                    break;
                case Webenq::COLLECTION_PRESENTATION_MULTIPLESELECT_LIST:
                    $element = new Zend_Form_Element_Multiselect($name);
                    break;
                case Webenq::COLLECTION_PRESENTATION_RANGESELECT_SLIDER:
                    $element = new ZendX_JQuery_Form_Element_Slider($name);
                    $element->setJQueryParams(
                        array(
                            'range' => true,
                            'min' => 0,
                            'max' => 100,
                            'values' => array(33, 67),
                        )
                    );
                    break;
                default:
                    throw new Exception(
                        'Element type "' . $qq->CollectionPresentation[0]->type .
                        '" (qq ' . $qq->id . ') not yet implemented in ' . get_class($this)
                    );
            }

            // add label
            $element->setLabel($this->Question->getQuestionText()->text);

            // add answer possibilities
            if ($element instanceof Zend_Form_Element_Multi) {
                $options = array();
                if ($element instanceof Zend_Form_Element_Select) {
                    $options[''] = '--- ' . t('select') . ' ---';
                }
                foreach ($this->AnswerPossibilityGroup->getAnswerPossibilities() as $possibility) {
                    $options[$possibility->id] = $possibility->getAnswerPossibilityText()->text;
                }
                $element->setMultiOptions($options);
            }

            // set filters
            if ($this->CollectionPresentation[0]->filters) {
                $filters = unserialize($this->CollectionPresentation[0]->filters);
                if (is_array($filters)) {
                    foreach ($filters as $name) {
                        $filter = Webenq::getFilterInstance($name);
                        $element->addFilter($filter);
                    }
                }
            }

            // set validators
            if ($this->CollectionPresentation[0]->validators) {
                $validators = unserialize($this->CollectionPresentation[0]->validators);
                if (is_array($validators)) {
                    foreach ($validators as $key => $value) {
                        if ($key=='validators'){
                            $name=$value;
                            $options=array();

                        }else{
                            $name=$key;
                            $options=$value;
                        }
                        $validator = Webenq::getValidatorInstance($name);
                        //@todo add options $validator->addOptions($options);
                        $element->addValidator($validator, true);
                        if ($validator instanceof Zend_Validate_NotEmpty) {
                            $element->setRequired(true);
                        }
                    }
                }
            }

            // add answer (if any)
            if ($this->hasAnswer($respondent)) {
                $answer = $this->getAnswer($respondent);
                if ($element instanceof Zend_Form_Element_Multi) {
                    $element->setValue($answer->answerPossibility_id);
                } else {
                    $element->setValue($answer->text);
                }
            }

            return $element;
        }
    }

    public function hasAnswer(Webenq_Model_Respondent $respondent = null)
    {
        if (!$respondent) return false;

        $count = Doctrine_Query::create()
        ->from('Webenq_Model_Answer a')
        ->where('a.questionnaire_question_id = ?', $this->id)
        ->andWhere('a.respondent_id = ?', $respondent->id)
        ->count();

        return ($count > 0);
    }

    /**
     * Returns the subquestions for the given questionnaire-question.
     * Note that the grouping of questions can be different for collection
     * mode and reporting mode. Therefor, the mode can be given as second
     * parameter. It defaults to the mode 'collection'.
     *
     * @param Webenq_Model_QuestionnaireQuestion|array $qq
     * @param $mode Can be 'collection' or 'report'
     * @return Doctrine_Collection containing instances of QuestionnaireQuestion
     */
    static public function getSubQuestions($qq, $mode = 'collection')
    {
        /* get current language from session */
        $session = new Zend_Session_Namespace();
        $language = $session->language;

        switch (strtolower($mode)) {

            case 'report':
                throw new Exception('Mode "report" is not yet implemented in QuestionnaireQuestion::getSubQuestions()');
                break;

            case 'collection':
            default:
                return Doctrine_Query::create()
                ->from('Webenq_Model_QuestionnaireQuestion qq')
                //->leftJoin('qq.Question q')
                //->leftJoin('q.QuestionText qt ON q.id = qt.question_id AND qt.language = ?', $language)
                ->innerJoin('qq.CollectionPresentation cp')
                ->where('cp.parent_id = ?', $qq['CollectionPresentation'][0]['id'])
                ->orderBy('cp.weight')
                ->execute();
        }
    }

    /**
     * Returns true if this question is present in more than one questionnaire,
     * and returns false otherwise.
     *
     * @return bool True or false
     */
    public function existsInMultipleQuestionnaires()
    {
        $count = Doctrine_Query::create()
        ->from('Webenq_Model_QuestionnaireQuestion qq')
        ->where('question_id = ?', $this->Question->id)
        ->execute()
        ->count();
        return ($count > 1);
    }

    public static function edit()
    {
        return 'test';
    }

    /**
     * @todo It now returns the parents of the current questionnaire-quesiton,
     * following the hierarchy of the data collection. In the future this should
     * be changed to reporting hierarchy. Data collection and data reporting
     * do not need to follow the same structure.
     *
     * @return array
     */
    protected function _getParents()
    {
        return $this->CollectionPresentation[0]->getParents();
    }

    /**
     * Returns the xpath to the current question for use in a xform
     *
     * @return string
     */
    public function getXpath()
    {
        $xpath = '/' . Webenq::Xmlify('questionnaire', 'tag'). '/';
        $parents = $this->_getParents();
        while (count($parents) > 0) {
            $parent = array_pop($parents);
            $xpath .=  Webenq::Xmlify('q' . $parent->QuestionnaireQuestion->id, 'tag').'/';
        }
        $xpath .=  Webenq::Xmlify('q' . $this->id, 'tag');
        return $xpath;
    }
    //new functions as of feb 2013
    /*
     * get answer options suggestions based on question text or all answeroptions
     * ordered by somerthing
     * @todo write function
     */
    public static function getAnswerOptions($questionText=null){
        return array('test'=>'empty');
    }

    /*
     * return the available presentation method based on type of answer possiblity
     *
     * @return array
     */
    public static function getAvailablePresentationMethod($type)
        {
        $availablePresentationMethod['choice']=array('radio/checkbox', 'pulldown','slider');
        $availablePresentationMethod['numeric']=array('open', 'slider');
        $availablePresentationMethod['text']=array('open');
        if (isset($availablePresentationMethod[$type])) {
            return $availablePresentationMethod[$type];
        } else {
            return array();
        }
    }

    /*
     * return question text
     *
     * @return string
     */

    public function getQuestionText()
    {
        return $this->Question->getQuestionText();
    }
    /*
     * get answer possiblity type
     *
     * @return string
     */
    public function getType()
    {
        return $this->CollectionPresentation[0]->type;
    }

    /*
     * get Webenq_model_questionnaireQuestion object by id
     *
     * @return Webenq_Model_QuestionnaireQuestion
     */
    public function find($id)
    {

        return Doctrine_Query::create()
            ->from(get_class($this))
            ->where('id= ?', $id)
            ->execute();

    }
    /**
     * Fills record with data in array and fills related objects with
     * translations
     *
     * @param array $array
     * @param bool $deep
     * @see Doctrine_Record::fromArray()
     * @todo write function
     */
    //from form to database
    public function fromArray(array $array, $deep = true)
    {
        parent::fromArray($array, $deep);

//         if (isset($array['question'])) {
//             foreach ($array['question'] as $language => $text) {
//                 if ($text && $language!='default_language') {
//                     $this->saveQuestionText($language, $text);
//                 }
//             }
//         }
        if (isset($array['question'])){
            $this->id=$array['question']['id'];
        }
    }
    //database to form
       /**
     * Fills array with data in record and fills related objects with
     * translations
     *
     * @param bool $deep
     * @param bool $prefixKey Not used
     * @return array
     * @see Doctrine_Record::fromArray()
     */
    public function toArray($deep = true, $prefixKey = false)
    {
        $result = parent::toArray($deep, $prefixKey);
        //text tab
        if (isset($result['Question']) && ($result['Question'])) {
            foreach ($result['Question'] as $question) {
                if (is_array($question)){
                    foreach ($question as $textOption){
                        if (isset($textOption['text']) && isset($textOption['language'])) {
                            $result['question'][$textOption['language']] = $textOption['text'];
                        }
                    }
                }
            }
        }
        if (isset($result['Question']) && ($result['Question'])){
            $result['question']['id']=$result['Question']['id'];
        }
        //answer options tab
        //@todo get reuse/suggestion value (answerDomain)
        //$result['answerOptions']['reuse']=$result['answerOptions']['suggestions']='';

        //options tab
        //@todo write options tab toArray-code
        return $result;
    }

    //public function save(){
//                 // type and answerPossiblitity group
//         if ($values['answers']['useAnswerPossibilityGroup'] == 1) {
//             $qq->answerPossibilityGroup_id = $values['answers']['answerPossibilityGroup_id'];
//             $cp->type = $values['type']['collectionPresentationType'];
//         } else {
//             $qq->answerPossibilityGroup_id = null;
//             $cp->type = $values['type']['collectionPresentationType'];
//             //$cp->type = Webenq::COLLECTION_PRESENTATION_OPEN_TEXT;
//         }
//         //TODO if collectionPresentationType is null set default type.
//         //layout
//         if (!isset($values['layout'])){
//             $values['layout']=array();
//         }
//         $cp->layout = serialize($values['layout']);


//         // get filters and validators (we don't use filters at this moment)
//         if (!isset($values['validation'])){
//             $values['validation']=array();
//         }
//         $cp->validators = serialize($values['validation']);

//         if (!isset($values['filters'])){
//             $values['filters']=array();
//         }
//         $cp->filters = serialize($filters);


//         //advanced
//         $qq->active=$values['advanced']['active'];
//         $qq->spss_variable_name=$values['advanced']['spss_variable_name'];
//         $qq->measurement_level=$values['advanced']['measurement_level'];


//         //obsolete:
//         // get move-to-value
//         $cp->parent_id = ($values['text']['moveTo'] > 0) ? $values['text']['moveTo'] : null;




//         $qq->save();

//    }

    /*
     * @todo adjust/check function we moved it from form to here.
     */
    public function saveQuestionText()
    {
        $qq = $this->_questionnaireQuestion;
        $cp = $qq->CollectionPresentation[0];

        $values = $this->getValues();

        //text
        /* check if the question texts have been modified */
        $isModifiedText = false;
        foreach ($qq->Question->QuestionText as $qt) {
            if (!key_exists($qt->language, $values['text']['text'])) {
                $isModifiedText = true;
                break;
            } elseif ($values['text']['text'][$qt->language] !== $qt->text) {
                $isModifiedText = true;
                break;
            }
        }

        /**
         * If text changes are set to be made locally, a copy of the
         * question is made and assigned to the current questionnaire.
         */
        if ($isModifiedText) {
            if ($values['text']['change_globally'] == 'local') {
                /* copy question */
                $question = new Webenq_Model_Question();
                unset($values['text']['change_globally']);
                foreach ($values['text']['text'] as $language => $text) {
                    $qt = new Webenq_Model_QuestionText;
                    $qt->text = $text;
                    $qt->language = $language;
                    $question->QuestionText[] = $qt;
                }
                $question->save();
                $qq->Question = $question;
            } else {
                // update or delete existing translations
                $texts = $values['text']['text'];
                foreach ($qq->Question->QuestionText as $qt) {
                    if (!key_exists($qt->language, $texts) || empty($texts[$qt->language])) {
                        $qt->delete();
                        unset($texts[$qt->language]);
                    } else {
                        $qt->text = $texts[$qt->language];
                        $qt->save();
                        unset($texts[$qt->language]);
                    }
                }
                // save new translations
                foreach ($texts as $language => $text) {
                    if (!empty($text)) {
                        $qt = new Webenq_Model_QuestionText();
                        $qt->language = $language;
                        $qt->text = $text;
                        $qt->question_id = $qq->question_id;
                        $qt->save();
                    }
                }
            }
        }
    }
}