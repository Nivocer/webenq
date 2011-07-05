<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_QuestionnaireQuestion_Delete extends Zend_Form
{
    /**
     * QuestionnaireQuestion instance
     *
     * @var QuestionnaireQuestion $_questionnaireQuestion
     */
    protected $_questionnaireQuestion;

    /**
     * Constructor
     *
     * @param QuestionnaireQuestion $questionnaireQuestion
     * @param mixed $options
     */
    public function __construct(QuestionnaireQuestion $questionnaireQuestion, $options = null)
    {
        $this->_questionnaireQuestion = $questionnaireQuestion;
        parent::__construct($options);
    }

    /**
     * Initialises the form
     *
     * @return void
     */
    public function init()
    {
        $qq = $this->_questionnaireQuestion;

        $this->addElements(array(
            $this->createElement('hidden', 'questionnaire_question_id', array(
                'decorators' => array('ViewHelper'),
                'value' => $this->_questionnaireQuestion->id,
            )),
            $this->createElement('submit', 'yes', array(
                'label' => 'ja',
                'decorators' => array('ViewHelper'),
            )),
            $this->createElement('submit', 'no', array(
                'label' => 'nee',
                'decorators' => array('ViewHelper'),
            )),
        ));

    }
}