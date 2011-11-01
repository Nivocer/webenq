<?php
/**
 * Questionnaire class definition
 *
 * @package    Webenq
 * @subpackage Models
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

//    public function getQuestionText()
//    {
//        if (!$this->_questionText) {
//            // find text in current language
//            $language = Zend_Registry::get('Zend_Locale')->getLanguage();
//            foreach ($this->Question->QuestionText as $text) {
//                if ($text->language === $language) {
//                    $this->_questionText = $text->text;
//                    break;
//                }
//            }
//            // or get default
//            if (!$this->_questionText) {
//                $this->_questionText = $this->Question->QuestionText[0]->text;
//            }
//        }
//        return $this->_questionText;
//    }

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
        $meta = unserialize($this->meta);
        switch ($meta['class']) {
            case 'Webenq_Model_Question_Closed_Scale_Two':
            case 'Webenq_Model_Question_Closed_Scale_Three':
            case 'Webenq_Model_Question_Closed_Scale_Four':
            case 'Webenq_Model_Question_Closed_Scale_Five':
            case 'Webenq_Model_Question_Closed_Scale_Six':
            case 'Webenq_Model_Question_Closed_Scale_Seven':
            case 'Webenq_Model_Question_Closed_Percentage':
                $element = $xml->createElement('select1');
                $element->setAttribute('ref', $this->getXpath());
                $label = $xml->createElement('label', Webenq::Xmlify($this->Question->QuestionText[0]->text));
                $element->appendChild($label);
                foreach ($this->AnswerPossibilityGroup->AnswerPossibility as $ap) {
                    $item = $xml->createElement('item');
                    $label = $xml->createElement('label', Webenq::Xmlify($ap->AnswerPossibilityText[0]->text));
                    $value = $xml->createElement('value', $ap->value);
                    $item->appendChild($label);
                    $item->appendChild($value);
                    $element->appendChild($item);
                }
                break;
            case null:
            case 'Webenq_Model_Question_Open_Text':
            case 'Webenq_Model_Question_Open_Email':
            case 'Webenq_Model_Question_Open_Date':
            case 'Webenq_Model_Question_Open_Number':
                $element = $xml->createElement('input');
                $element->setAttribute('ref', $this->getXpath());
                $label = $xml->createElement('label', Webenq::Xmlify($this->Question->QuestionText[0]->text));
                $element->appendChild($label);
                break;
            default:
                throw new Exception(__METHOD__ . ' not yet implemented for ' . $meta['class']);
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
        $meta = unserialize($this->meta);
        switch ($meta['class']) {

            case 'Webenq_Model_Question_Closed_Scale_Two':
            case 'Webenq_Model_Question_Closed_Scale_Three':
            case 'Webenq_Model_Question_Closed_Scale_Four':
            case 'Webenq_Model_Question_Closed_Scale_Five':
            case 'Webenq_Model_Question_Closed_Scale_Six':
            case 'Webenq_Model_Question_Closed_Scale_Seven':
            case 'Webenq_Model_Question_Closed_Percentage':
                $element = $xml->createElement(Webenq::Xmlify('q' . $this->id, 'tag'));
                break;

            case null:
            case 'Webenq_Model_Question_Open_Text':
            case 'Webenq_Model_Question_Open_Email':
            case 'Webenq_Model_Question_Open_Date':
            case 'Webenq_Model_Question_Open_Number':
                $element = $xml->createElement(Webenq::Xmlify('q' . $this->id, 'tag'));
                break;

            default:
                throw new Exception(__METHOD__ . ' not yet implemented for ' . $meta['class']);
        }

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
        $meta = unserialize($this->meta);
        switch ($meta['class']) {
            case 'Webenq_Model_Question_Closed_Scale_Two':
            case 'Webenq_Model_Question_Closed_Scale_Three':
            case 'Webenq_Model_Question_Closed_Scale_Four':
            case 'Webenq_Model_Question_Closed_Scale_Five':
            case 'Webenq_Model_Question_Closed_Scale_Six':
            case 'Webenq_Model_Question_Closed_Scale_Seven':
            case 'Webenq_Model_Question_Closed_Percentage':
                $element = $xml->createElement('bind');
                $element->setAttribute('nodeset', $this->getXpath());
                $element->setAttribute('type', 'select1');
                break;
            case null:
            case 'Webenq_Model_Question_Open_Text':
            case 'Webenq_Model_Question_Open_Email':
            case 'Webenq_Model_Question_Open_Date':
            case 'Webenq_Model_Question_Open_Number':
                $element = $xml->createElement('bind');
                $element->setAttribute('nodeset', $this->getXpath());
                $element->setAttribute('type', 'string');
                break;
            default:
                throw new Exception(__METHOD__ . ' not yet implemented for ' . $meta['class']);
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
                $element->nodeValue = $answer->answerPossibility_id;
            } else {
                $element->nodeValue = $answer->text;
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
     * @param Webenq_Model_Respondent $respondent (optional) If provided the form element is filled with the respondent's answer
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
                case Webenq::COLLECTION_PRESENTATION_OPEN_TEXT:
                    $element = new Zend_Form_Element_Text($name);
                    break;
                case Webenq::COLLECTION_PRESENTATION_OPEN_TEXTAREA:
                    $element = new Zend_Form_Element_Textarea($name);
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
                    $element->setJQueryParams(array(
                        'value' => '50'
                    ));
                    break;
                case Webenq::COLLECTION_PRESENTATION_MULTIPLESELECT_CHECKBOXES:
                    $element = new Zend_Form_Element_MultiCheckbox($name);
                    break;
                case Webenq::COLLECTION_PRESENTATION_MULTIPLESELECT_LIST:
                    $element = new Zend_Form_Element_Multiselect($name);
                    break;
                case Webenq::COLLECTION_PRESENTATION_RANGESELECT_SLIDER:
                    $element = new ZendX_JQuery_Form_Element_Slider($name);
                    $element->setJQueryParams(array(
                        'range' => true,
                        'min' => 0,
                        'max' => 100,
                        'values' => array(33, 67),
                    ));
                    break;
                default:
                    throw new Exception('Element type "' . $qq->CollectionPresentation[0]->type . '" (qq ' . $qq->id .
                        ') not yet implemented in ' . get_class($this));
            }

            // add label
            $element->setLabel($this->Question->getQuestionText()->text);

            // add answer possibilities
            if ($element instanceof Zend_Form_Element_Multi) {
                $options = array();
                if ($element instanceof Zend_Form_Element_Select) {
                    $options[''] = '--- ' . t('select') . ' ---';
                }
                foreach ($this->AnswerPossibilityGroup->AnswerPossibility as $possibility) {
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
                    foreach ($validators as $name) {
                        $validator = Webenq::getValidatorInstance($name);
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
                    ->leftJoin('qq.Question q')
                    ->leftJoin('q.QuestionText qt ON q.id = qt.question_id AND qt.language = ?', $language)
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
        $xpath = '/' . Webenq::Xmlify('questionnaire', 'tag');
        $parents = $this->_getParents();
        while (count($parents) > 0) {
            $parent = array_pop($parents);
            $xpath .= '/' . Webenq::Xmlify('q' . $parent->QuestionnaireQuestion->id, 'tag');
        }
        $xpath .= '/' . Webenq::Xmlify('q' . $this->id, 'tag');
        return $xpath;
    }
}