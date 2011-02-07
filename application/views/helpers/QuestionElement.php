<?php
class Zend_View_Helper_QuestionElement extends Zend_View_Helper_Abstract
{
	/**
	 * Helper for rendering form elements
	 * 
	 * @param QuestionnaireQuestion $qq Doctrine object
	 * @param bool $deep Indicating if childs elements should be rendered as well
	 * @return Zend_Form_Element or string
	 */
	public function questionElement(QuestionnaireQuestion $qq, $deep = true)
	{
		if ($deep == false) {
			return $this->_getElement($qq);
		}
		
		/* get form element */
		$elm = array($this->_getElement($qq));
		
		/* get collection-presentation objects for child questions */
		$subQqsCp = Doctrine_Query::create()
			->from('CollectionPresentation cp')
			->where('cp.parent_id = ?', $qq->CollectionPresentation[0]->id)
			->orderBy('cp.weight')
			->execute();
			
		if ($subQqsCp->count() > 0) {
			foreach ($subQqsCp as $cp1) {
				
				/* get form element for current sub question */
				$subElm = array($this->_getElement($cp1->QuestionnaireQuestion));
				
				/* get collection-presentation objects for child questions */
				$subSubQqsCp = Doctrine_Query::create()
					->from('CollectionPresentation cp')
					->where('cp.parent_id = ?', $cp1->id)
					->orderBy('cp.weight')
					->execute();
				
				if ($subSubQqsCp->count() > 0) {
					foreach ($subSubQqsCp as $cp2) {
						$subElm[] = $this->_getElement($cp2->QuestionnaireQuestion);
					}
					$elm[] = $subElm;
				}
			}
		}
		
		if (count($elm) == 1 && is_object($elm[0])) {
			return $elm[0];
		} else {
			return $this->_getMultiElementTable($elm);
		}
	}
	
	protected function _getElement(QuestionnaireQuestion $qq)
	{
		$elementName = 'qq_' . $qq->id;
		
		/* set default element type if not yet set */
		if (!$qq->CollectionPresentation[0]->type) {
			if (!$qq->answerPossibilityGroup_id) {
				$qq->CollectionPresentation[0]->type = Webenq::COLLECTION_PRESENTATION_OPEN_TEXT;
			} else {
				$qq->CollectionPresentation[0]->type = Webenq::COLLECTION_PRESENTATION_SINGLESELECT_RADIOBUTTONS;
			}
			$qq->save();
		}
		
		/* instantiate form element */
		switch ($qq->CollectionPresentation[0]->type) {
			case Webenq::COLLECTION_PRESENTATION_OPEN_TEXT:
				$element = new Zend_Form_Element_Text($elementName);
				break;
			case Webenq::COLLECTION_PRESENTATION_OPEN_TEXTAREA:
				$element = new Zend_Form_Element_Textarea($elementName);
				break;
			case Webenq::COLLECTION_PRESENTATION_OPEN_DATE:
				$element = new ZendX_JQuery_Form_Element_DatePicker($elementName);
				$element->addFilter(new Webenq_Filter_Date());
				break;
			case Webenq::COLLECTION_PRESENTATION_OPEN_CURRENTDATE:
				$element = new Webenq_Form_Element_CurrentDate($elementName);
				$element->removeDecorator('Label');
				break;
			case Webenq::COLLECTION_PRESENTATION_SINGLESELECT_RADIOBUTTONS:
				$element = new Zend_Form_Element_Radio($elementName);
				break;
			case Webenq::COLLECTION_PRESENTATION_SINGLESELECT_DROPDOWNLIST:
				$element = new Zend_Form_Element_Select($elementName);
				break;
			case Webenq::COLLECTION_PRESENTATION_SINGLESELECT_SLIDER:
				$element = new ZendX_JQuery_Form_Element_Slider($elementName);
				$element->setJQueryParams(array(
					'value' => '50'
				));
				break;
			case Webenq::COLLECTION_PRESENTATION_MULTIPLESELECT_CHECKBOXES:
				$element = new Zend_Form_Element_MultiCheckbox($elementName);
				break;
			case Webenq::COLLECTION_PRESENTATION_MULTIPLESELECT_LIST:
				$element = new Zend_Form_Element_Multiselect($elementName);
				break;
			case Webenq::COLLECTION_PRESENTATION_RANGESELECT_SLIDER:
				$element = new ZendX_JQuery_Form_Element_Slider($elementName);
				$element->setJQueryParams(array(
					'range' => true,
					'min' => 0,
					'max' => 100,
					'values' => array(33, 67),
				));
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
		
		/* set filters */
		if ($qq->CollectionPresentation[0]->filters) {
			$filters = unserialize($qq->CollectionPresentation[0]->filters);
			if (is_array($filters)) {
				foreach ($filters as $name) {
					$filter = Webenq::getFilterInstance($name);
					$element->addFilter($filter);
				}
			}
		}
		
		/* set validators */
		if ($qq->CollectionPresentation[0]->validators) {
			$validators = unserialize($qq->CollectionPresentation[0]->validators);
			if (is_array($validators)) {
				foreach ($validators as $name) {
					$validator = Webenq::getValidatorInstance($name);
					$element->addValidator($validator, true);
					if ($validator instanceof Zend_Validate_NotEmpty) {
						$element->setRequired(true);
					}
				}
			}
		}
		
		return $element;
	}
	
	protected function _getMultiElementTable(array $elements)
	{
		$html = '<table><thead><tr><th colspan="' . $this->_getTableWidth($elements) . '">';
		$html .= $elements[0]->getLabel();
		$html .= '</th></tr></thead><tbody>';
		
		unset($elements[0]);
		foreach ($elements as $row) {
			$html .= '<tr>';
			foreach ($row as $i => $col) {
				if ($i == 0) {
					$html .= '<td>' . $col->getLabel() . '</td>';
				} else {
					if ($i > 1 && $this->_equalElements($col, $row[$i-1])) {
						var_dump($col); die;
					} else {
						$html .= '<td>' . $col->render() . '</td>';
					}
				}
			}
			$html .= '</tr>';
		}
		$html .= '</tbody></table>';
		
		return $html;
	}
	
	protected function _equalElements(Zend_Form_Element $elm1, Zend_Form_Element $elm2)
	{
		if (get_class($elm1) == get_class($elm2)) {
			if ($elm1->getLabel() == $elm2->getLabel()) {
				if ($elm1 instanceof Zend_Form_Element_Multi) {
					if ($elm1->getMultiOptions() == $elm2->getMultiOptions()) {
						return true;
					}
				} else {
					return true;
				}
			}
		}
		return false;
	}
	
	protected function _getTableWidth($elements)
	{
		$max = 0;
		foreach ($elements as $element) {
			if (count($element) > $max) $max = count($element);
		}
		return $max;
	}
}