<?php
class HVA_Form_QuestionnaireQuestion_Edit extends Zend_Form
{
	/**
	 * Constructor
	 * 
	 * @param QuestionnaireQuestion $questionnaireQuestion
	 * @param mixed $options
	 */
	public function __construct(QuestionnaireQuestion $questionnaireQuestion, $options = null)
	{
		parent::__construct($options);
		
		$this->id->setValue($questionnaireQuestion->id);
		$this->collectionPresentationType->setValue(
			$questionnaireQuestion->CollectionPresentation[0]->type
		);
		$this->required->setValue(
			$questionnaireQuestion->CollectionPresentation[0]->required
		);
		$this->reportPresentationType->setValue(
			$questionnaireQuestion->ReportPresentation[0]->type
		);
	}
	
	/**
	 * Initialises the form
	 * 
	 * @return void
	 */
	public function init()
	{
		$collectionPresentationTypes = array(
			'open' => array(
				COLLECTION_PRESENTATION_OPEN_TEXT => 'text field',
				COLLECTION_PRESENTATION_OPEN_TEXTAREA => 'text area',
				COLLECTION_PRESENTATION_OPEN_DATE => 'date selector',
				COLLECTION_PRESENTATION_OPEN_CURRENTDATE => 'current date',
			),
			'single select' => array(
				COLLECTION_PRESENTATION_SINGLESELECT_DROPDOWNLIST => 'drop-down list',
				COLLECTION_PRESENTATION_SINGLESELECT_RADIOBUTTONS => 'radio buttons',
			),
			'multiple select' => array(
				COLLECTION_PRESENTATION_MULTIPLESELECT_LIST => 'list',
				COLLECTION_PRESENTATION_MULTIPLESELECT_CHECKBOXES => 'checkboxes',
			),
		);
		
		$reportPresentationTypes = array(
			'single select' => array(
				COLLECTION_PRESENTATION_SINGLESELECT_DROPDOWNLIST => 'drop-down list',
				COLLECTION_PRESENTATION_SINGLESELECT_RADIOBUTTONS => 'radio buttons',
			),
			'multiple select' => array(
				COLLECTION_PRESENTATION_MULTIPLESELECT_LIST => 'list',
				COLLECTION_PRESENTATION_MULTIPLESELECT_CHECKBOXES => 'checkboxes',
			),
		);
		
		$this->addElements(array(
			$this->createElement('hidden', 'id'),
			$this->createElement('select', 'collectionPresentationType', array(
				'label' => 'Type:',
				'multiOptions' => $collectionPresentationTypes,
			)),
			$this->createElement('checkbox', 'required', array(
				'label' => 'Required:',
			)),
			$this->createElement('select', 'reportPresentationType', array(
				'label' => 'Type:',
				'multiOptions' => $reportPresentationTypes,
			)),
			$this->createElement('submit', 'submit', array(
				'value' => 'opslaan',
				'order' => 100,
			)),
		));
		
		$this->addDisplayGroup(
			array('collectionPresentationType', 'required'),
			'collectionPresentation',
			array('legend' => 'Datacollectie')
		);
		$this->addDisplayGroup(
			array('reportPresentationType'),
			'reportPresentation',
			array('legend' => 'Rapportage')
		);
	}
}