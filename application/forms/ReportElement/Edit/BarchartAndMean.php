<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>, Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_ReportElement_Edit_BarchartAndMean extends Webenq_Form_ReportElement_Edit
{
    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        $multiOptions = array();
        foreach ($this->_element->Report->Questionnaire->QuestionnaireQuestion as $qq) {
            $multiOptions[$qq->id] = $qq->Question->getQuestionText()->text;
        }

        $this->addElement(
            $this->createElement(
                'select',
                'header_qq_id',
                array(
                    'label' => 'header question',
                    'required' => true,
                    'multiOptions' => $multiOptions,
                )
            )
        );

        $this->addElement(
            $this->createElement(
                'multiCheckbox',
                'report_qq_ids',
                array(
                    'label' => 'reporting questions',
                    'required' => true,
                    'multiOptions' => $multiOptions,
                )
            )
        );

        $this->addElement(
            $this->createElement(
                'select',
                'color_mean',
                array(
                    'label' => 'color the means',
                    'required' => true,
                    'multiOptions' => array(
                        'no' => 'no color',
                        'yes' => 'colored by mean'
                    ),
                )
            )
        );

        $this->addElement(
            $this->createElement(
                'submit',
                'submit',
                array(
                    'label' => 'save',
                )
            )
        );
    }
}