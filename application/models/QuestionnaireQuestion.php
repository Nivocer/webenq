<?php
/**
 * Questionnaire class definition
 *
 * @package    Webenq
 * @subpackage Models
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Model_QuestionnaireQuestion extends QuestionnaireQuestion
{
    /**
     * Class constructor
     *
     * Defaults to new entry
     *
     * @param Doctrine_Table $table
     * @param bool $isNewEntry
     */
    public function __construct($table = null, $isNewEntry = true)
    {
        parent::__construct($table, $isNewEntry);
    }

    public function getFormElement()
    {
        $elementName = "qq_$this->id";

        /* set default element type if not yet set */
        if (!$this->CollectionPresentation[0]->type) {
            if (!$this->answerPossibilityGroup_id) {
                $this->CollectionPresentation[0]->type = Webenq::COLLECTION_PRESENTATION_OPEN_TEXT;
            } else {
                $this->CollectionPresentation[0]->type = Webenq::COLLECTION_PRESENTATION_SINGLESELECT_RADIOBUTTONS;
            }
//            $this->CollectionPresentation[0]->save();
        }

        /* instantiate form element */
        switch ($this->CollectionPresentation[0]->type) {
            case Webenq::COLLECTION_PRESENTATION_OPEN_TEXT:
                $element = new Zend_Form_Element_Text($elementName);
                break;
            case Webenq::COLLECTION_PRESENTATION_OPEN_TEXTAREA:
                $element = new Zend_Form_Element_Textarea($elementName);
                break;
            case Webenq::COLLECTION_PRESENTATION_OPEN_DATE:
                $element = new ZendX_JQuery_Form_Element_DatePicker($elementName);
                $element->addFilter(new Webenq_Filter_Date());
                break;
            case Webenq::COLLECTION_PRESENTATION_OPEN_CURRENTDATE:
                $element = new Webenq_Form_Element_CurrentDate($elementName);
                $element->removeDecorator('Label');
                break;
            case Webenq::COLLECTION_PRESENTATION_SINGLESELECT_RADIOBUTTONS:
                $element = new Zend_Form_Element_Radio($elementName);
                break;
            case Webenq::COLLECTION_PRESENTATION_SINGLESELECT_DROPDOWNLIST:
                $element = new Zend_Form_Element_Select($elementName);
                break;
            case Webenq::COLLECTION_PRESENTATION_SINGLESELECT_SLIDER:
                $element = new ZendX_JQuery_Form_Element_Slider($elementName);
                $element->setJQueryParams(array(
                    'value' => '50'
                ));
                break;
            case Webenq::COLLECTION_PRESENTATION_MULTIPLESELECT_CHECKBOXES:
                $element = new Zend_Form_Element_MultiCheckbox($elementName);
                break;
            case Webenq::COLLECTION_PRESENTATION_MULTIPLESELECT_LIST:
                $element = new Zend_Form_Element_Multiselect($elementName);
                break;
            case Webenq::COLLECTION_PRESENTATION_RANGESELECT_SLIDER:
                $element = new ZendX_JQuery_Form_Element_Slider($elementName);
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

        /* add label */
        if (isset($this->Question->QuestionText[0])) {
            $element->setLabel($this->Question->QuestionText[0]->text);
        } else {
            $element->setLabel(_('No question text available for the current language'));
        }

        /* add answer possibilities */
        if ($element instanceof Zend_Form_Element_Multi) {
            $options = array();
            if ($element instanceof Zend_Form_Element_Select) {
                $options[''] = '--- selecteer ---';
            }
            if (isset($this->AnswerPossibilityGroup)) {
                foreach ($this->AnswerPossibilityGroup->AnswerPossibility as $possibility) {
                    if (isset($possibility->AnswerPossibilityText[0])) {
                        $options[$possibility->id] = $possibility->AnswerPossibilityText[0]->text;
                    } else {
                        $options[$possibility->id] =
                            _('No answer possibility text available for the current language');
                    }
                }
            }
            $element->setMultiOptions($options);
        }

        /* set filters */
        if ($this->CollectionPresentation[0]->filters) {
            $filters = unserialize($this->CollectionPresentation[0]->filters);
            if (is_array($filters)) {
                foreach ($filters as $name) {
                    $filter = Webenq::getFilterInstance($name);
                    $element->addFilter($filter);
                }
            }
        }

        /* set validators */
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