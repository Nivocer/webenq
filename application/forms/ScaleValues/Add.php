<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_ScaleValues_Add extends Zend_Form
{
    public function init()
    {
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage("Enter a label");

        $label = $this->createElement('text', 'label');
        $label->setLabel('Label:')
        ->setRequired(true)
        ->addValidator($notEmpty);

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage("Enter a value");

        $value = $this->createElement('text', 'value');
        $value->setLabel('Value:')
        ->setRequired(true)
        ->addValidator($notEmpty);

        $questionType = $this->createElement('select', 'question_type');
        $questionType->setLabel('Question type:')
        ->addMultiOptions(
            array(
                        'Webenq_Model_Data_Question_Closed_Scale_Two'    => '2-punts schaal',
                        'Webenq_Model_Data_Question_Closed_Scale_Three'    => '3-punts schaal',
                        'Webenq_Model_Data_Question_Closed_Scale_Four'    => '4-punts schaal',
                        'Webenq_Model_Data_Question_Closed_Scale_Five'    => '5-punts schaal',
                        'Webenq_Model_Data_Question_Closed_Scale_Six'    => '6-punts schaal',
                        'Webenq_Model_Data_Question_Closed_Scale_Seven'    => '7-punts schaal',
                )
        );

        $submit = $this->createElement('submit', 'submit');
        $submit->setLabel('Save');

        $this->addElements(array($label, $value, $questionType, $submit));
    }
}