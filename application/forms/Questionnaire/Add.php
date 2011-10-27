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
        foreach (Webenq_Language::getLanguages() as $language) {
            $this->addElement($this->createElement('text', $language, array(
                'belongsTo' => 'title',
                'label' => "Titel ($language):",
                'filters' => array('StringTrim'),
                'validators' => array(
                    new Zend_Validate_NotEmpty(),
                    new Zend_Validate_Alnum(true),
                ),
            )));
        }

        $this->addElement($this->createElement('submit', 'submit', array(
            'label' => 'opslaan',
        )));
    }

    public function isValid($values)
    {
        // at least one language is required
        $hasAtLeastOneLanguage = false;
        foreach ($values['title'] as $language) {
            if (!empty($language)) {
                $hasAtLeastOneLanguage = true;
                break;
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