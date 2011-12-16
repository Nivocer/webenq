<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_Report_Add extends Zend_Form
{
    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        $title = new Zend_Form_SubForm(array('legend' => 'report title'));
        $this->addSubForm($title, 'title');

        $languages = Webenq_Language::getLanguages();
        foreach ($languages as $language) {
            $title->addElement($this->createElement('text', $language, array(
                'label' => $language . ':',
                'size' => 60,
                'maxlength' => 255,
                'autocomplete' => 'off',
                'required' => true,
                'filters' => array('StringTrim'),
                'validators' => array('NotEmpty'),
            )));
        }

        $this->addElement($this->createElement('select', 'questionnaire_id', array(
            'label' => 'questionnaire',
        	'required' => true,
            'multiOptions' => Webenq_Model_Questionnaire::getKeyValuePairs(),
        )));

        $this->addElement($this->createElement('select', 'language', array(
            'label' => 'language',
        	'required' => true,
            'multiOptions' => $languages,
        )));

        $this->addElement($this->createElement('select', 'customer', array(
            'label' => 'customer',
        	'required' => true,
            'multiOptions' => array('leeuwenburg' => 'leeuwenburg'),
        )));

        $this->addElement($this->createElement('select', 'output_dir', array(
            'label' => 'output directory',
        	'required' => true,
            'multiOptions' => array('tmpout/test' => 'tmpout/test'),
        )));

        $this->addElement($this->createElement('text', 'output_name', array(
            'label' => 'output file name',
        	'required' => true,
        )));

        $this->addElement($this->createElement('select', 'output_format', array(
            'label' => 'output format',
        	'required' => true,
            'multiOptions' => array('pdf' => 'pdf'),
        )));

        $this->addElement($this->createElement('submit', 'submit', array(
            'label' => 'save',
        )));
    }

    public function isValid($data)
    {
        // disable required setting if at least one language is set
        foreach ($data['title'] as $language => $translation) {
            if ($translation) {
                foreach ($this->getSubForm('title')->getElements() as $elm) {
                    $elm->setRequired(false);
                }
                break;
            }
        }

        return parent::isValid($data);
    }
}