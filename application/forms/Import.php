<?php
class Webenq_Form_Import extends Zend_Form
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
    public function __construct(array $supportedFormats, $options = null) {
        parent::__construct($options);
        $this->_supportedFormats = $supportedFormats;
        $this->_buildForm();
    }


    /**
     * Builds the form
     */
    protected function _buildForm()
    {
        $this->setAttrib('enctype', 'multipart/form-data');

        $notEmpty = new Zend_Validate_NotEmpty();
        $count = new Zend_Validate_File_Count(array(
            'min' => 1,
            'max' => 1));
        $extension = new Zend_Validate_File_Extension($this->_supportedFormats);

        $file = $this->createElement('file', 'file');
        $file
            ->setRequired(true)
            ->setLabel('Selecteer het te importeren databestand: ')
            ->setDescription('De volgende bestandsindelingen worden ondersteund: ' .
                implode(', ', $this->_supportedFormats))
            ->addValidators(array(
                $notEmpty,
                $count,
                $extension
            ));

        $type = $this->createElement('radio', 'type', array(
            'label' => 'Type:',
            'value' => 'default',
            'multiOptions' => Webenq_Import_Abstract::$supportedTypes,
        ));

        $language = $this->createElement('radio', 'language', array(
            'label' => 'Taal:',
            'multiOptions' => Webenq_Language::getLanguages(),
            'required' => true,
        ));

        $submit = $this->createElement('submit', 'submit', array('value' => 'Importeren'));

        $this->addElements(array($file, $type, $language, $submit));
    }


    /**
     * Validates the form
     */
    public function isValid($data)
    {
        $files = $this->file->getTransferAdapter()->getFileInfo();
        if (preg_match('/csv$/', strtolower($files['file']['name']))) {
            if ($data['type'] != 'default') {
                $this->file->addError('Bestanden met formaat CSV kunnen alleen van het type "default" zijn');
                return false;
            }
        }
        return parent::isValid($data);
    }
}