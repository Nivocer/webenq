<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_Email_Merge extends Zend_Form
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
        $count = new Zend_Validate_File_Count(
            array(
                'min' => 1,
                'max' => 1
            )
        );
        $extension = new Zend_Validate_File_Extension($this->_supportedFormats);

        $file = $this->createElement('file', 'file');
        $file
            ->setRequired(true)
            ->setLabel('Select the file with email-addresses')
            ->setDescription(
                'The next file formats are supported' .
                implode(', ', $this->_supportedFormats)
            )
            ->addValidators(
                array(
                    $notEmpty,
                    $count,
                    $extension
                )
            );

        $submit = $this->createElement('submit', 'Import');

        $this->addElements(array($file, $submit));
    }
}