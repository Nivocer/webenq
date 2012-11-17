<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_ReportElement_Edit_Text extends Webenq_Form_ReportElement_Edit
{
    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        $this->addElement($this->createElement('textarea', 'text', array(
                'label' => 'text',
                'required' => true,
        )));

        $this->addElement($this->createElement('multiCheckbox', 'report_for_subgroup', array(
                'label' => 'Display text if we create reports for subgroups (split)',
                'required' => false,
                'multiOptions' => array("onlyForSubGroups"=>"Only for subgroups"),
        )));


        $this->addElement($this->createElement('submit', 'submit', array(
                'label' => 'save',
        )));
    }
}