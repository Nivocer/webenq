<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_Email_Index extends Zend_Form
{
    protected $_subDirs = array();

    public function __construct($subDirs, $options = null)
    {
        $this->_subDirs = $subDirs;
        parent::__construct($options);
    }

    public function init()
    {
        $selectDir = new Zend_Form_Element_Select('selectDir');
        $selectDir->setLabel('Selecteer een directory:')
            ->setMultiOptions(array('' => ''))
            ->addMultiOptions($this->_subDirs);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('rapporten weergeven');

        $this->addElements(array($selectDir, $submit));
    }
}