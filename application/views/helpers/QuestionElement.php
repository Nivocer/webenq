<?php
class Zend_View_Helper_QuestionElement extends Zend_View_Helper_Abstract
{
	public function questionElement(QuestionnaireQuestion $qq)
	{
		$elementName = 'qq_' . $qq->id;
		
		/* set default element type if not yet set */
		if (!$qq->CollectionPresentation[0]->type) {
			if (!$qq->answerPossibilityGroup_id) {
				$qq->CollectionPresentation[0]->type = COLLECTION_PRESENTATION_OPEN_TEXT;
			} else {
				$qq->CollectionPresentation[0]->type = COLLECTION_PRESENTATION_SINGLESELECT_RADIOBUTTONS;
			}
			$qq->save();
		}
		
		/* instantiate form element */
		switch ($qq->CollectionPresentation[0]->type) {
			case COLLECTION_PRESENTATION_OPEN_TEXT:
				$element = new Zend_Form_Element_Text($elementName);
				break;
			case COLLECTION_PRESENTATION_OPEN_TEXTAREA:
				$element = new Zend_Form_Element_Textarea($elementName);
				break;
			case COLLECTION_PRESENTATION_OPEN_DATE:
				$element = new ZendX_JQuery_Form_Element_DatePicker($elementName);
				$element->addFilter(new Webenq_Filter_Date());
				break;
			case COLLECTION_PRESENTATION_OPEN_CURRENTDATE:
				$element = new Webenq_Form_Element_CurrentDate($elementName);
				$element->removeDecorator('Label');
				break;
			case COLLECTION_PRESENTATION_MULTIPLESELECT_CHECKBOXES:
				$element = new Zend_Form_Element_MultiCheckbox($elementName);
				break;
			case COLLECTION_PRESENTATION_MULTIPLESELECT_LIST:
				$element = new Zend_Form_Element_Multiselect($elementName);
				break;
			case COLLECTION_PRESENTATION_SINGLESELECT_RADIOBUTTONS:
				$element = new Zend_Form_Element_Radio($elementName);
				break;
			case COLLECTION_PRESENTATION_SINGLESELECT_DROPDOWNLIST:
				$element = new Zend_Form_Element_Select($elementName);
				break;
			default:
				throw new Exception('Element type "' . $qq->CollectionPresentation[0]->type . '" (qq ' . $qq->id . ') not yet implemented in ' . get_class($this));
		}
		
		/* add label */
		$element->setLabel($qq->Question->QuestionText[0]->text);
		
		/* add answer possibilities */
		if ($element instanceof Zend_Form_Element_Multi) {
			$options = array();
			foreach ($qq->AnswerPossibilityGroup->AnswerPossibility as $possibility) {
				$options[$possibility->id] = $possibility->AnswerPossibilityText[0]->text;
			}
			$element->setMultiOptions($options);
		}
		
		/* set required */
		if ($qq->CollectionPresentation[0]->required) {
			$element->setRequired(true)
				->addValidator(new Zend_Validate_NotEmpty());
		}
		
		return $element;
	}
}