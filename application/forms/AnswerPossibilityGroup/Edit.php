<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>, Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_AnswerPossibilityGroup_Edit extends Zend_Form
{
    /**
     * Current answer-possibility-group
     *
     * @var Webenq_Model_AnswerPossibilityGroup $_answerPossibilityGroup
     */
    protected $_answerPossibilityGroup;

    /**
     * Class constructor
     *
     * @param Webenq_Model_AnswerPossibilityGroup $answerPossibilityGroup
     * @param array|Zend_Config $options
     * @return void
     */
    public function __construct(Webenq_Model_AnswerPossibilityGroup $answerPossibilityGroup, array $options = null)
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

        $this->addElements(
            array(
                $this->createElement(
                    'hidden',
                    'id',
                    array(
                        'value' => $this->_answerPossibilityGroup->id,
                    )
                ),
                $this->createElement(
                    'text',
                    'name',
                    array(
                        'label' => 'name',
                        'value' => $this->_answerPossibilityGroup->name,
                    )
                ),
                $this->createElement(
                    'text',
                    'number',
                    array(
                        'label' => 'number of allowed answers',
                        'value' => $this->_answerPossibilityGroup->number,
                        'validators' => array('Int'),
                    )
                ),
                $this->createElement(
                    'radio',
                    'measurement_level',
                    array(
                    'label' => 'measurement level',
                    'multiOptions' => array(
                        'metric' => 'metric',
                        'non-metric' => 'non-metric',
                    ),
                    'value' => $this->_answerPossibilityGroup->measurement_level,
                    'required' => true,
                    'validators' => array('NotEmpty'),
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