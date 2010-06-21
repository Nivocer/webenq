<?php
class HVA_Form_ScaleValues_Add extends Zend_Form
{
	public function init()
	{
		$notEmpty = new Zend_Validate_NotEmpty();
		$notEmpty->setMessage("Geef een label op");
		
		$label = $this->createElement('text', 'label');
		$label->setLabel('Label:')
			->setRequired(true)
			->addValidator($notEmpty);
		
		$notEmpty = new Zend_Validate_NotEmpty();
		$notEmpty->setMessage("Geef een waarde op");
		
		$value = $this->createElement('text', 'value');
		$value->setLabel('Waarde:')
			->setRequired(true)
			->addValidator($notEmpty);
		
		$questionType = $this->createElement('select', 'question_type');
		$questionType->setLabel('Vraagtype:')
			->addMultiOptions(array(
				'HVA_Model_Data_Question_Closed_Scale_Two'	=> '2-punts schaal',
				'HVA_Model_Data_Question_Closed_Scale_Three'=> '3-punts schaal',
				'HVA_Model_Data_Question_Closed_Scale_Four'	=> '4-punts schaal',
				'HVA_Model_Data_Question_Closed_Scale_Five'	=> '5-punts schaal',
				'HVA_Model_Data_Question_Closed_Scale_Six'	=> '6-punts schaal',
				'HVA_Model_Data_Question_Closed_Scale_Seven'=> '7-punts schaal',
			));
		
		$submit = $this->createElement('submit', 'submit');
		$submit->setLabel('Opslaan');
		
		$this->addElements(array($label, $value, $questionType, $submit));
	}
}