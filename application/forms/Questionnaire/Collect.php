<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_Questionnaire_Collect extends Zend_Form
{
    /**
     * Array with questions
     *
     * @var array
     */
    protected $_questions;

    /**
     *
     * @param array|Doctrine_Collection $questions
     * @param array $options
     */
    public function __construct($questions, $options = null)
    {
        if ($questions instanceof Doctrine_Collection) {
            $this->_questions = $questions->toArray();
        } elseif (is_array($questions)) {
            $this->_questions = $questions;
        } else {
            throw new Exception('First parameter must be an array or an instance of Doctrine_Collection!');
        }
        parent::__construct($options);
    }

    public function init()
    {
        $view = $this->getView();

        /* iterate over questions */
        foreach ($this->_questions as $values) {

            // instantiate question object
            $question = new Webenq_Model_QuestionnaireQuestion();
            $question->fromArray($values, false);

            /* get sub-questions */
            $subQuestions = QuestionnaireQuestion::getSubQuestions($question);

            if (!isset($subQuestions[0])) {
                /* if no sub-questions: add element */
                $this->addElement($question->getFormElement());
            } else {
                /* if sub-questions: add subform */
                $subForm = new Zend_Form_SubForm();
                $subForm->setLegend($question['Question']['QuestionText'][0]['text'])
                    ->removeDecorator('DtDdWrapper');

                /* iterate over sub-questions */
                foreach ($subQuestions as $subQuestion) {

                    /* get sub-sub-questions */
                    $subSubQuestions = QuestionnaireQuestion::getSubQuestions($subQuestion);

                    if (!isset($subSubQuestions[0])) {
                        /* if no sub-sub-questions: add element */
                        $subForm->addElement($view->questionElement($subQuestion, false));
                    } else {
                        /* if sub-sub-questions: add subform */
                        $subSubForm = new Zend_Form_SubForm();
                        $subSubForm->setLegend($subQuestion['Question']['QuestionText'][0]['text'])
                            ->removeDecorator('DtDdWrapper');

                        /* prepare wrapper decorator */
                        $wrapper = new Zend_Form_Decorator_HtmlTag();
                        $wrapper->setTag('div');
                        $percentage = floor(100/count($subSubQuestions));
                        $wrapper->setOption('style', "float: left; width: $percentage%;");

                        /* iterate over sub-sub-questions */
                        foreach ($subSubQuestions as $subSubQuestion) {
                            $elm = $view->questionElement($subSubQuestion, false);
                            $elm->addDecorator(array('Wrapper' => $wrapper));
                            $subSubForm->addElement($elm);
                        }
                        $subForm->addSubForm($subSubForm, $subQuestion['Question']['QuestionText'][0]['text']);
                    }
                }
                $this->addSubForm($subForm, $question['Question']['QuestionText'][0]['text']);
            }
        }

        $this->addElement($this->createElement('submit', 'submit', array(
            'label' => 'verder',
        )));
    }

    /**
     * Retrieve a single element
     *
     * @param  string $name
     * @return Zend_Form_Element|null
     */
    public function getElement($name)
    {
        $element = parent::getElement($name);
        if ($element) {
            return $element;
        } else {
            $subForms = $this->getSubForms();
            foreach ($subForms as $subForm) {
                $element = $subForm->getElement($name);
                if ($element) {
                    return $element;
                } else {
                    $subSubForms = $subForm->getSubForms();
                    foreach ($subSubForms as $subSubForm) {
                        $element = $subSubForm->getElement($name);
                        if ($element) {
                            return $element;
                        }
                    }
                }
            }
        }
        return null;
    }
}