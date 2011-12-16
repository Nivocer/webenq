<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_Report_Edit extends Webenq_Form_Report_Add
{
    public function init()
    {
        parent::init();
        $this->addElement($this->createElement('select', 'split_qq_id', array(
            'label' => 'split question',
            'order' => 2,
        )));
    }
}