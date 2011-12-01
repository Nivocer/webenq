<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_Questionnaire_Add extends Zend_Form
{
    public function init()
    {
        $regex = new Zend_Validate_Regex('/^[\w\d\s\?\.\+\-_,=\'"]*$/');
        $regex->setMessage('The title contains invalid characters, valid characters are: a-z A-Z 1-9 _ - = + \' " . , ?', Zend_Validate_Regex::NOT_MATCH);

        foreach (Webenq_Language::getLanguages() as $language) {
            $this->addElement($this->createElement('text', $language, array(
                'belongsTo' => 'title',
                'label' => t('title') . " ($language)",
                'filters' => array('StringTrim'),
                'validators' => array(new Zend_Validate_NotEmpty(), $regex),
            )));
        }

        $this->addElement($this->createElement('submit', 'submit', array(
            'label' => 'save',
        )));
    }

    public function isValid($values)
    {
        // at least one language is required
        $hasAtLeastOneLanguage = false;
        if (isset($values['title'])) {
            foreach ($values['title'] as $language) {
                if (!empty($language)) {
                    $hasAtLeastOneLanguage = true;
                    break;
                }
            }
        }
        if (!$hasAtLeastOneLanguage) {
            $elements = $this->getElements();
            $firstElement = array_shift($elements);
            $firstElement->setRequired();
        }

        return parent::isValid($values);
    }
}