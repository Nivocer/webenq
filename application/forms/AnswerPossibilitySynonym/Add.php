<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_AnswerPossibilitySynonym_Add extends Zend_Form
{
    /**
     * Current answer-possibility-text
     *
     * @var Webenq_Model_AnswerPossibilityText $_answerPossibilityText
     */
    protected $_answerPossibilityText;

    /**
     * Class constructor
     *
     * @param Webenq_Model_AnswerPossibilityText $_answerPossibilityText
     * @param array|Zend_Config $options
     * @return void
     */
    public function __construct(Webenq_Model_AnswerPossibilityText $answerPossibilityText, array $options = null)
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
        $this->addElements(
            array(
                $this->createElement(
                    'hidden',
                    'answerPossibilityText_id',
                    array(
                        'value' => $this->_answerPossibilityText->id,
                    )
                ),
                $this->createElement(
                    'text',
                    'text',
                    array(
                        'label' => t('synonym for') . ' "' . $this->_answerPossibilityText->text . '"',
                        'required' => true,
                    )
                ),
                $this->createElement(
                    'submit',
                    'submit',
                    array(
                        'label' => 'save',
                    )
                ),
            )
        );
    }
}