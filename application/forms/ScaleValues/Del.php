<?php
class Webenq_Form_ScaleValues_Del extends Zend_Form
{
	public function init()
	{
    	$confirm = new Zend_Form_Element_Submit('confirm');
    	$confirm->setLabel("ja, verwijderen")->setValue("yes");
    	$this->addElement($confirm);
	}
}