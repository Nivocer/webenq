<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_AnswerPossibilityGroup_Add extends Zend_Form
{
    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        $this->addElements(array(
            $this->createElement('text', 'name', array(
                'label' => 'name',
                'required' => true,
            )),
            $this->createElement('radio', 'measurement_level', array(
                'label' => 'measurement level',
                'multiOptions' => array(
                    'metric' => 'metric',
                    'non-metric' => 'non-metric',
                ),
                'value' => 'non-metric',
                'required' => true,
                'validators' => array('NotEmpty'),
            )),
            $this->createElement('submit', 'submit', array(
                'label' => 'save',
            )),
        ));
    }
}