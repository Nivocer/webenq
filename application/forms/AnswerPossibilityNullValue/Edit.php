<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_AnswerPossibilityNullValue_Edit extends Webenq_Form_AnswerPossibilityNullValue_Add
{
    /**
     * Current answer-possibility-null-value
     *
     * @var AnswerPossibilityNullValue $_answerPossibilityNullValue
     */
    protected $_answerPossibilityNullValue;

    /**
     * Class constructor
     *
     * @param Webenq_Model_AnswerPossibilityNullValue $answerPossibilityNullValue
     * @param array|Zend_Config $options
     * @return void
     */
    public function __construct(
            Webenq_Model_AnswerPossibilityNullValue $answerPossibilityNullValue,
            array $options = null
        )
    {
        $this->_answerPossibilityNullValue = $answerPossibilityNullValue;
        parent::__construct($options);
    }

    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->setAttrib('autocomplete', 'off');

        $this->getElement('value')->setValue($this->_answerPossibilityNullValue->value);

        $this->addElements(
            array(
                $this->createElement(
                    'hidden',
                    'id',
                    array(
                        'value' => $this->_answerPossibilityNullValue->id,
                    )
                ),
            )
        );
    }
}