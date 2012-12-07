<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_ReportGeneration_Index extends Zend_Form
{
    protected $_subDirs = array();

    public function __construct($subDirs, $options = null)
    {
        $this->_subDirs = $subDirs;
        parent::__construct($options);
    }

    public function init()
    {
        $createDir = new Zend_Form_Element_Text('createDir');
        $createDir->setLabel('Create a new directory:');

        $selectDir = new Zend_Form_Element_Select('selectDir');
        $selectDir->setLabel('Select an existing directory:')
            ->setMultiOptions(array('' => ''))
            ->addMultiOptions($this->_subDirs);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('generate reports');

        $this->addElements(array($createDir, $selectDir, $submit));
    }

    public function isValid($data)
    {
        if (!$data['createDir'] && !$data['selectDir']) {
            $this->getElement('selectDir')->addError('Select a directory');
            return false;
        }

        if ($data['createDir'] && $data['selectDir']) {
            $this->getElement('selectDir')->addError('Select just one directory');
            return false;
        }

        return parent::isValid($data);
    }
}