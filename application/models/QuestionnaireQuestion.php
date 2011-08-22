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
    public function getXformsElement(DOMDocument $xml, DOMElement $group = null)
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
                $elm = $xml->createElement('select1');
                $elm->setAttribute('ref', $this->getXpath());
                $label = $xml->createElement('label', Webenq::Xmlify($this->Question->QuestionText[0]->text));
                $elm->appendChild($label);
                foreach ($this->AnswerPossibilityGroup->AnswerPossibility as $ap) {
                    $item = $xml->createElement('item');
                    $label = $xml->createElement('label', Webenq::Xmlify($ap->AnswerPossibilityText[0]->text));
                    $value = $xml->createElement('value', $ap->value);
                    $item->appendChild($label);
                    $item->appendChild($value);
                    $elm->appendChild($item);
                }
                break;

            case null:
            case 'Webenq_Model_Question_Open_Text':
            case 'Webenq_Model_Question_Open_Email':
            case 'Webenq_Model_Question_Open_Date':
            case 'Webenq_Model_Question_Open_Number':
                $elm = $xml->createElement('input');
                $elm->setAttribute('ref', $this->getXpath());
                $label = $xml->createElement('label', Webenq::Xmlify($this->Question->QuestionText[0]->text));
                $elm->appendChild($label);
                break;

            default:
                throw new Exception(__METHOD__ . ' not yet implemented for ' . $meta['class']);
        }

        $subQqs = Webenq_Model_QuestionnaireQuestion::getSubQuestions($this);
        if ($subQqs->count() > 0) {

            $originalElm = $elm;
            $elm = $xml->createElement('group');
            $elm->setAttribute('ref', $this->getXpath());
            foreach ($originalElm->childNodes as $item) {
                $elm->appendChild($item);
            }

            foreach ($subQqs as $subQq) {
                $subElm = $subQq->getXformsElement($xml, $elm);
                $elm->appendChild($subElm);
            }
        }
        return $elm;
    }

    public function getXformsInstanceElement(DOMDocument $xml, DOMElement $group = null)
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
                $elm = $xml->createElement(Webenq::Xmlify($this->Question->QuestionText[0]->text));
                break;

            case null:
            case 'Webenq_Model_Question_Open_Text':
            case 'Webenq_Model_Question_Open_Email':
            case 'Webenq_Model_Question_Open_Date':
            case 'Webenq_Model_Question_Open_Number':
                $elm = $xml->createElement(Webenq::Xmlify($this->Question->QuestionText[0]->text));
                break;

            default:
                throw new Exception(__METHOD__ . ' not yet implemented for ' . $meta['class']);
        }

        $subQqs = Webenq_Model_QuestionnaireQuestion::getSubQuestions($this);
        if ($subQqs->count() > 0) {
            foreach ($subQqs as $subQq) {
                $subElm = $subQq->getXformsInstanceElement($xml, $elm);
                $elm->appendChild($subElm);
            }
        }
        return $elm;
    }

    public function getXformsBindElements(DOMDocument $xml, array $elms = array())
    {
        $elms = array();
        $meta = unserialize($this->meta);
        switch ($meta['class']) {

            case 'Webenq_Model_Question_Closed_Scale_Two':
            case 'Webenq_Model_Question_Closed_Scale_Three':
            case 'Webenq_Model_Question_Closed_Scale_Four':
            case 'Webenq_Model_Question_Closed_Scale_Five':
            case 'Webenq_Model_Question_Closed_Scale_Six':
            case 'Webenq_Model_Question_Closed_Scale_Seven':
            case 'Webenq_Model_Question_Closed_Percentage':
                $elm = $xml->createElement('bind');
                $elm->setAttribute('nodeset', $this->getXpath());
                $elm->setAttribute('type', 'select1');
                break;

            case null:
            case 'Webenq_Model_Question_Open_Text':
            case 'Webenq_Model_Question_Open_Email':
            case 'Webenq_Model_Question_Open_Date':
            case 'Webenq_Model_Question_Open_Number':
                $elm = $xml->createElement('bind');
                $elm->setAttribute('nodeset', $this->getXpath());
                $elm->setAttribute('readonly', 'true()');
                $elm->setAttribute('type', 'string');
                break;

            default:
                throw new Exception(__METHOD__ . ' not yet implemented for ' . $meta['class']);
        }
        $elms[] = $elm;

        $subQqs = Webenq_Model_QuestionnaireQuestion::getSubQuestions($this);
        if ($subQqs->count() > 0) {
            foreach ($subQqs as $subQq) {
                $elms = array_merge($subQq->getXformsBindElements($xml, $elms), $elms);
            }
        }

        return $elms;
    }

    /**
     * Returns an instance of Zend_Form_SubForm if it has sub-questions,
     * and an instance of Zend_Form_Element otherwise.
     *
     * @return Zend_Form_SubForm|Zend_Form_Element
     */
    public function getFormElement()
    {
        $name = "qq_$this->id";
        $subQuestions = self::getSubQuestions($this);

        if ($subQuestions->count() > 0) {

            // create subform
            $subForm = new Zend_Form_SubForm();
            $subForm->setLegend($this->Question->QuestionText[0]->text)
                ->removeDecorator('DtDdWrapper');

            // add child questions to subform
            foreach ($subQuestions as $subQuestion) {
                $name = "qq_$subQuestion->id";
                $elm = $subQuestion->getFormElement();
                if ($elm instanceof Zend_Form_Element) {
                    $subForm->addElement($elm, $name);
                } else {
                    $subForm->addSubForm($elm, $name);
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
            $element->setLabel($this->Question->getQuestionText());

            // add answer possibilities
            if ($element instanceof Zend_Form_Element_Multi) {
                $options = array();
                if ($element instanceof Zend_Form_Element_Select) {
                    $options[''] = '--- selecteer ---';
                }
                foreach ($this->AnswerPossibilityGroup->AnswerPossibility as $possibility) {
                    if (isset($possibility->AnswerPossibilityText[0])) {
                        $options[$possibility->id] = $possibility->AnswerPossibilityText[0]->text;
                    } else {
                        $options[$possibility->id] =
                            _('No answer possibility text available for the current language');
                    }
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
            return $element;
        }
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

    public function getXpath()
    {
        $questionnaire = $this->Questionnaire;
        $title = Webenq::Xmlify($questionnaire->title);

        $xpath = '';
        $parents = $this->_getParents($this->CollectionPresentation[0]);
        while (count($parents) > 0) {
            $parent = array_pop($parents);
            $text = Webenq::Xmlify($parent->QuestionnaireQuestion->Question->QuestionText[0]->text);
            $xpath .= "$text/";
        }
        $xpath .= Webenq::Xmlify($this->Question->QuestionText[0]->text);
        return "/$title/$xpath";
    }

    protected function _getParents(Webenq_Model_CollectionPresentation $cp, array $parents = array())
    {
        $parent = $cp->Parent;
        if ($parent->id) {
            $parents[] = $parent;
            $this->_getParents($parent, $parents);
        }
        return $parents;
    }
}