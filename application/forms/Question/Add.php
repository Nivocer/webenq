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
		$language = $this->createElement('select', 'language', array(
			'label' => 'Taal:',
			'multiOptions' => array(
				'nl' => 'nl',
			),
		));
		
		$text = $this->createElement('text', 'text', array(
			'label' => 'Tekst:',
		));
		
		$submit = $this->createElement('submit', 'submit', array(
			'label' => 'Opslaan',
		));
		
		$this->addElements(array($language, $text, $submit));
	}
}