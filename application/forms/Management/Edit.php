<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_Management_Edit extends Zend_Form
{
    /**
     * Questionnaire question
     */
    protected $_questionnaireQuestion;

    /**
     * Class constructor
     *
     * @param Webenq_Model_QuestionnaireQuestion $questionnaireQuestion
     * @param array $options Zend_Form options
     * @return void
     */
    public function __construct(Webenq_Model_QuestionnaireQuestion $questionnaireQuestion, $options = null)
    {
        $this->_questionnaireQuestion = $questionnaireQuestion;
        parent::__construct($options);
    }


    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        $questionnaireQuestion = $this->_questionnaireQuestion;

        /* needed to show the default checked radio button in FireFox */
        $this->setAttrib("autocomplete", "off");

        $label = $questionnaireQuestion->Question->QuestionText[0]->text;
        if ($questionnaireQuestion->meta) {
            $meta = unserialize($questionnaireQuestion->meta);
            $valid = $meta['valid'];
            $class = $meta['class'];
        } else {
            $class = '';
        }

        $elm = new Zend_Form_Element_Radio('class');
        $elm->setLabel($label)
            ->setRequired(true)
            ->setValue(array_search($class, $valid))
            ->addMultiOptions($valid);
        $this->addElement($elm);

        $submit = new Zend_Form_Element_Submit("submit", "submit");
        $this->addElement($submit);
    }
}