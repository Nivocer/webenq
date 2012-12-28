<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
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

        $notEmpty = new Zend_Validate_NotEmpty();
        $count = new Zend_Validate_File_Count(array('min' => 1, 'max' => 1));
        $extension = new Zend_Validate_File_Extension($this->_supportedFormats);

        $file = $this->createElement('file', 'file');
        $file->setRequired(true)->setLabel('select the file to import')->setDescription(
            t('supported formats') . ': '
            . implode(', ', $this->_supportedFormats)
        )
        ->addValidators(array($notEmpty, $count, $extension));

        $type = $this->createElement(
            'radio', 'type', array(
                'label' => 'type',
                'value' => 'default',
                'multiOptions' => Webenq_Import_Abstract::$supportedTypes,
            )
        );

        $language = $this->createElement(
            'radio', 'language', array(
                'label' => 'language',
                'multiOptions' => Webenq_Language::getLanguages(),
                'required' => true,
            )
        );

        $submit = $this->createElement('submit', 'submit', array('value' => 'Import'));

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
                $this->file->addError(t('Files in CSV format should be of the default type'));
                return false;
            }
        }
        return parent::isValid($data);
    }
}