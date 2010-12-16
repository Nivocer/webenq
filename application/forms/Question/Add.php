<?php
class HVA_Form_Question_Add extends Zend_Form
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
			$text->addElement(
				$text->createElement('text', $language, array(
					'label' => 'Tekst (' . $language . '):',
					'size' => 60,
					'maxlength' => 255,
					'required' => true,
					'validators' => array(
						new Zend_Validate_NotEmpty(),
					),
				))
			);
		}
		
		$this->addElement(
			$this->createElement('submit', 'submit', array(
				'label' => 'Opslaan',
			))
		);
	}
}