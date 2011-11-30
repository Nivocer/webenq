<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_Tool_Hva extends Zend_Form
{
    /**
     * Supported input formats
     */
    protected $_supportedFormats = array();

    /**
     * Class constructor
     *
     * @param array $supportedFormats Formats allowed for file upload
     * @param array $options Zend_Form options
     */
    public function __construct(array $supportedFormats, $options = null)
    {
        $this->_supportedFormats = $supportedFormats;
        parent::__construct($options);
    }

    /**
     * Builds the form
     */
    public function init()
    {
        $this->setAttrib('enctype', 'multipart/form-data');

        $file = $this->createElement('file', 'file');
        $file->setMultiFile(5)
            ->addValidator(new Zend_Validate_File_Count(array('min' => 1, 'max' => 5)))
            ->addValidator(new Zend_Validate_File_Extension($this->_supportedFormats))
            ->setLabel('select the file(s) to import')
            ->setDescription(t('supported formats') . ': '
            	. implode(', ', $this->_supportedFormats));

        $submit = $this->createElement('submit', 'submit', array('value' => 'Importeren'));

        $this->addElements(array($file, $submit));
    }
}