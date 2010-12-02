<?php
class HVA_Form_Questionnaire_Add extends Zend_Form
{
	public function init()
	{
		$this->addElements(array(
			$this->createElement('text', 'title', array(
				'label' => 'Titel:',
			)),
			$this->createElement('submit', 'submit', array(
				'label' => 'opslaan',
			)),
		));
	}
}