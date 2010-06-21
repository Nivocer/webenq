<?php

class HVA_Form_ReportDefinition extends Zend_Form
{
	/**
	 * Questions
	 */
	protected $_questions = array();
	
	
	/**
	 * Questions
	 */
	protected $_outputFormats = array();
	
	
	/**
	 * Questions
	 */
	protected $_reportTypes = array();
	
	
	/**
	 * Class constructor
	 * 
	 * @param array $questions
	 * @param array $outputFormats
	 * @param array $reportTypes
	 * @param array $options Zend_Form options
	 * @return void
	 */
	public function __construct(array $questions, $outputFormats, $reportTypes, $options = null)
	{		
		parent::__construct($options);
		
		$this->_questions = $questions;
		$this->_outputFormats = $outputFormats;
		$this->_reportTypes = $reportTypes;
		
		$this->_buildForm();
	}


	/**
	 * Builds the form
	 */
	protected function _buildForm()
	{
		/* needed to show the default checked radio button in FireFox */
		$this->setAttrib("autocomplete", "off");
		
    	$filenameFilter = new Zend_Filter_PregReplace("#[^A-Za-z0-9_-]#", "_");
		
		$filename = new Zend_Form_Element_Text('output_filename');
    	$filename->setLabel('Geef de bestandsnaam voor het rapport op (zonder extensie):')
    		->addFilter($filenameFilter);
		
		$output = new Zend_Form_Element_Radio('output_format');
    	$output
    		->setLabel('Selecteer een rapport formaat:')
    		->setMultiOptions($this->_outputFormats)
    		->setRequired(true);
    		
    	$report = new Zend_Form_Element_Radio('report_type');
    	$report
    		->setLabel('Selecteer een rapport-type:')
    		->setMultiOptions($this->_reportTypes)
    		->setRequired(true);
    		
    	$group = new Zend_Form_Element_Select('group_question_id');
    	$group
    		->setLabel('Selecteer een vraag om de data te groeperen:')
    		->setRequired(false)
    		->setMultiOptions(array('' => '--- geen groepering ---'))
    		->addMultiOptions($this->_questions);
    	
    	$split = new Zend_Form_Element_Select('split_question_id');
    	$split
    		->setLabel('Selecteer een vraag om de data te splitsen:')
    		->setRequired(false)
    		->setMultiOptions(array('' => '--- geen splitsing ---'))
    		->addMultiOptions($this->_questions);
    	
    	$ignore = new Zend_Form_Element_MultiCheckbox('ignore_question_ids');
    	$ignore
    		->setLabel('Selecteer de vragen die niet de rapportage moeten worden opgenomen:')
    		->setRequired(false)
    		->addMultiOptions($this->_questions);
    	
    	$submit = new Zend_Form_Element_Submit('submit');
    	$submit->setLabel('Verzenden');
    	
    	$this->addElements(array($filename, $output, $report, $group, $split, $ignore, $submit));
	}
}