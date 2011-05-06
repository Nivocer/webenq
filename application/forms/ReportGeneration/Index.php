<?php
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
        $createDir->setLabel('Maak een nieuwe directory:');
        
        $selectDir = new Zend_Form_Element_Select('selectDir');
        $selectDir->setLabel('Selecteer een bestaande directory:')
            ->setMultiOptions(array('' => ''))
            ->addMultiOptions($this->_subDirs);
            
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('genereer rapporten');
        
        $this->addElements(array($createDir, $selectDir, $submit));        
    }
    
    public function isValid($data)
    {
        if (!$data['createDir'] && !$data['selectDir']) {
            $this->getElement('selectDir')->addError('Geef een directory op');
            return false;
        }
        
        if ($data['createDir'] && $data['selectDir']) {
            $this->getElement('selectDir')->addError('Geef slechts een directory op');
            return false;
        }
        
        return parent::isValid($data);
    }
}