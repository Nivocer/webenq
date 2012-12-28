<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_Category_Add extends Zend_Form
{
    public function init()
    {
        foreach (Webenq_Language::getLanguages() as $language) {
            $this->addElement(
                $this->createElement(
                    'text',
                    $language,
                    array(
                        'belongsTo' => 'text',
                        'label' => t('title') . " ($language)",
                        'filters' => array('StringTrim'),
                        'validators' => array(
                            new Zend_Validate_NotEmpty()
                        ),
                    )
                )
            );
        }
        /*$this->addElement(
            $this->createElement(
                'radio',
                'default_language',
                array(
                    'label' => 'Default language',
                    'required' => false,
                    'value' => Zend_Registry::get('Zend_Locale')->getLanguage(),
                    'multiOptions' => Webenq_Language::getLanguages(),

                )
            )
        );

*/
        $this->addElement(
            $this->createElement(
                'checkbox',
                'active',
                array(
                    'label' => 'Active',
                    'checked' => (isset($this->_category->active)&& $this->_category->active) ? true : false,
                )
            )
        );

        $this->addElement(
            $this->createElement(
                'submit',
                'submit',
                array(
                    'label' => 'save',
                )
            )
        );
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