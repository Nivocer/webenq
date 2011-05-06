<?php
class Webenq_Form_AnswerPossibilityGroup_Edit extends Zend_Form
{
    /**
     * Current answer-possibility-group
     * 
     * @var AnswerPossibilityGroup $_answerPossibilityGroup
     */
    protected $_answerPossibilityGroup;
    
    /**
     * Class constructor
     * 
     * @param AnswerPossibilityGroup $answerPossibilityGroup
     * @param array|Zend_Config $options
     * @return void
     */
    public function __construct(AnswerPossibilityGroup $answerPossibilityGroup, array $options = null)
    {
        $this->_answerPossibilityGroup = $answerPossibilityGroup;
        parent::__construct($options);
    }
    
    /**
     * Builds the form
     * 
     * @return void
     */
    public function init()
    {
        $this->setAttrib('autocomplete', 'off');
        
        $this->addElements(array(
            $this->createElement('hidden', 'id', array(
                'value' => $this->_answerPossibilityGroup->id,
            )),
            $this->createElement('text', 'name', array(
                'label' => 'Naam:',
                'value' => $this->_answerPossibilityGroup->name,
            )),
            $this->createElement('radio', 'measurement_level', array(
                'label' => 'Meetniveau:',
                'multiOptions' => array(
                    'metric' => 'metric',
                    'non-metric' => 'non-metric',
                ),
                'value' => $this->_answerPossibilityGroup->measurement_level,
                'required' => true,
                'validators' => array('NotEmpty'),
            )),
            $this->createElement('submit', 'submit', array(
                'label' => 'opslaan',
            )),
        ));
    }
}