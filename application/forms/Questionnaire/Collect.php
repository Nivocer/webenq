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
     * @var Doctrine_Collection
     */
    protected $_questions;

    /**
     *
     * @param Doctrine_Collection $questions
     * @param array $options
     */
    public function __construct(Doctrine_Collection $questions, $options = null)
    {
        $this->_questions = $questions;
        parent::__construct($options);
    }

    public function init()
    {
        // add questions
        foreach ($this->_questions as $question) {
            $name = "qq_$question->id";
            $elm = $question->getFormElement();
            if ($elm instanceof Zend_Form_Element) {
                $this->addElement($elm, $name);
            } elseif ($elm instanceof Zend_Form_SubForm) {
                $this->addSubform($elm, $name);
            }
        }

        // add submit button
        $this->addElement($this->createElement('submit', 'submit', array(
            'label' => 'verder',
        )));
    }

    /**
     * Retrieve a single element
     *
     * @param  string $name
     * @return Zend_Form_Element|null
     * @todo make recursive
     */
    public function getElement($name)
    {
        $element = parent::getElement($name);
        if ($element) {
            return $element;
        } else {
            foreach ($this->getSubForms() as $subForm) {
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