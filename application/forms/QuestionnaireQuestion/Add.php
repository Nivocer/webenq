<?php
class Webenq_Form_QuestionnaireQuestion_Add extends Zend_Form
{
    /**
     * Id of the current questionnaire
     * 
     * @var int $_questionnaireId
     */
    protected $_questionnaireId;
    
    /**
     * Constructor
     * 
     * @param int $questionnaireId Questionnaire to which the question must be added
     * @param mixed $options
     */
    public function __construct($questionnaireId, $options = null)
    {
        $this->_questionnaireId = $questionnaireId;
        parent::__construct($options);
    }
    
    /**
     * Initialises the form
     * 
     * @return void
     */
    public function init()
    {
        $this->addElements(array(
            $this->createElement('hidden', 'id', array(
                'required' => true,
                'decorators' => array('ViewHelper'),
            )),
            $this->createElement('hidden', 'questionnaire_id', array(
                'required' => true,
                'value' => $this->_questionnaireId,
                'decorators' => array('ViewHelper'),
            )),
            $this->createElement('text', 'filter', array(
                'label' => 'Filter:',
                'autocomplete' => 'off',
            )),
        ));
    }
}