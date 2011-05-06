<?php
class Webenq_Form_AnswerPossibilitySynonym_Edit extends Zend_Form
{
    /**
     * Current answer-possibility-text-synonym
     * 
     * @var AnswerPossibilityTextSynonym $_synonym
     */
    protected $_synonym;
    
    /**
     * Class constructor
     * 
     * @param AnswerPossibilityTextSynonym $_synonym
     * @param array|Zend_Config $options
     * @return void
     */
    public function __construct(AnswerPossibilityTextSynonym $synonym, array $options = null)
    {
        $this->_synonym = $synonym;
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
            $this->createElement('hidden', 'id', array(
                'value' => $this->_synonym->id,
            )),
            $this->createElement('text', 'text', array(
                'label' => 'Tekst:',
                'value' => $this->_synonym->text,
                'required' => true,
            )),
            $this->createElement('submit', 'submit', array(
                'label' => 'opslaan',
            )),
        ));
    }
}