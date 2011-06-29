<?php
class Webenq_Form_Question_Add extends Zend_Form
{
    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        $text = new Zend_Form_SubForm();
        $text->setDecorators(array('FormElements'));
        $this->addSubForm($text, 'text');

        $languages = Webenq_Language::getLanguages();
        foreach ($languages as $language) {
            $text->addElement($this->createElement('text', $language, array(
                'label' => 'Tekst (' . $language . '):',
                'size' => 60,
                'maxlength' => 255,
                'autocomplete' => 'off',
                'required' => true,
                'validators' => array(
                    new Zend_Validate_NotEmpty(),
                ),
            )));
        }

        $this->addElement($this->createElement('submit', 'submit', array(
            'label' => 'Opslaan',
        )));
    }

    public function isValid($data)
    {
        // check if at least one language is filled out
        $hasAtLeastOneLanguage = false;
        foreach ($data['text'] as $language => $translation) {
            if (trim($translation) != '') {
                $hasAtLeastOneLanguage = true;
                break;
            }
        }

        // disable required setting if at least one language was found
        if ($hasAtLeastOneLanguage) {
            foreach ($this->getSubForm('text')->getElements() as $elm) {
                $elm->setRequired(false);
            }
        }

        return parent::isValid($data);
    }
}