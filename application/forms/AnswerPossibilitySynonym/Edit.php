<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_AnswerPossibilitySynonym_Edit extends Zend_Form
{
    /**
     * Current answer-possibility-text-synonym
     *
     * @var Webenq_Model_AnswerPossibilityTextSynonym $_synonym
     */
    protected $_synonym;

    /**
     * Class constructor
     *
     * @param Webenq_Model_AnswerPossibilityTextSynonym $_synonym
     * @param array|Zend_Config $options
     * @return void
     */
    public function __construct(Webenq_Model_AnswerPossibilityTextSynonym $synonym, array $options = null)
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
                'label' => 'text',
                'value' => $this->_synonym->text,
                'required' => true,
            )),
            $this->createElement('submit', 'submit', array(
                'label' => 'save',
            )),
        ));
    }
}