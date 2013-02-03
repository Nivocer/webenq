<?php
/**
 * WebEnq4
 *
 *  LICENSE
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    Webenq_Data_Import
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Form class
 *
 * @package    Webenq_Data_Import
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
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