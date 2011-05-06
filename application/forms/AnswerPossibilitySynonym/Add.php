<?php
class Webenq_Form_AnswerPossibilitySynonym_Add extends Zend_Form
{
    /**
     * Current answer-possibility-text
     * 
     * @var AnswerPossibilityText $_answerPossibilityText
     */
    protected $_answerPossibilityText;
    
    /**
     * Class constructor
     * 
     * @param AnswerPossibilityText $_answerPossibilityText
     * @param array|Zend_Config $options
     * @return void
     */
    public function __construct(AnswerPossibilityText $answerPossibilityText, array $options = null)
    {
        $this->_answerPossibilityText = $answerPossibilityText;
        parent::__construct($options);
    }
    
    /**
     * Builds the form
     * 
     * @return void
     */
    public function init()
    {
        $this->addElements(array(
            $this->createElement('hidden', 'answerPossibilityText_id', array(
                'value' => $this->_answerPossibilityText->id,
            )),
            $this->createElement('text', 'text', array(
                'label' => 'Synoniem voor "' . $this->_answerPossibilityText->text . '":',
                'required' => true,
            )),
            $this->createElement('submit', 'submit', array(
                'label' => 'opslaan',
            )),
        ));
    }
}