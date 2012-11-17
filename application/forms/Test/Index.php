<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_Test_Index extends Zend_Form
{
    /**
     * Builds the form
     */
    public function init()
    {
        $this->setAttrib('enctype', 'multipart/form-data');

        $notEmpty = new Zend_Validate_NotEmpty();
        $count = new Zend_Validate_File_Count(
            array(
                'min' => 1,
                'max' => 1)
        );

        $file = $this->createElement('file', 'file');
        $file
        ->setRequired(true)
        ->setLabel('Selecteer een bestand met testdata:')
        ->addValidators(
            array(
               $notEmpty,
               $count,
                )
        );

        $submit = $this->createElement('submit', 'Test');

        $this->addElements(array($file, $submit));
    }
}