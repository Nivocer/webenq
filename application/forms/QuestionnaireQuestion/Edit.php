<?php
class HVA_Form_QuestionnaireQuestion_Edit extends Zend_Form
{
	/**
	 * QuestionnaireQuestion instance
	 * 
	 * @var QuestionnaireQuestion $_questionnaireQuestion
	 */
	protected $_questionnaireQuestion;
	
	/**
	 * Constructor
	 * 
	 * @param QuestionnaireQuestion $questionnaireQuestion
	 * @param mixed $options
	 */
	public function __construct(QuestionnaireQuestion $questionnaireQuestion, $options = null)
	{
		$this->_questionnaireQuestion = $questionnaireQuestion;
		parent::__construct($options);
	}
	
	/**
	 * Initialises the form
	 * 
	 * @return void
	 */
	public function init()
	{
		$qq = $this->_questionnaireQuestion;
		$cp = $qq->CollectionPresentation[0];
		$rp = $qq->ReportPresentation[0];
		
		/* insert elements from form for general question settings */
		$questionForm = new HVA_Form_Question_Edit($qq->Question);
		$subform = $questionForm->getSubForm('text');
		$subform->addElement(
			$subform->createElement('radio', 'change_globally', array(
				'label' => 'Hoe wilt u bovenstaande wijzigingen doorvoeren?',
				'value' => 'global',
				'multiOptions' => array(
					'global' => 'Alle questionnaires',
					'local' => 'Alleen de huidige questionnaire',
				),
			))
		);
		$this->addSubForm($subform, 'text');
		
		$this->addElements(array(
			$this->createElement('hidden', 'id', array(
				'value' => $qq->id,
			)),
			$this->createElement('checkbox', 'useAnswerPossibilityGroup', array(
				'label' => 'Maak gebruik van vaste antwoordmogelijkheden:',
			)),
			$this->createElement('select', 'answerPossibilityGroup_id', array(
				'label' => 'Selecteer een groep met antwoordmogelijkheden:',
				'multiOptions' => AnswerPossibilityGroup::getAll(),
				'value' => $qq->answerPossibilityGroup_id,
			)),
			$this->createElement('select', 'collectionPresentationType', array(
				'label' => 'Type:',
				'multiOptions' => Webenq::getCollectionPresentationTypes(),
				'value' => $cp->type,
			)),
			$this->createElement('multiCheckbox', 'filters', array(
				'label' => 'Filters:',
				'multiOptions' => Webenq::getFilters(),
				'value' => unserialize($cp->filters),
			)),
			$this->createElement('multiCheckbox', 'validators', array(
				'label' => 'Validatie:',
				'multiOptions' => Webenq::getValidators(),
				'value' => unserialize($cp->validators),
			)),
			$this->createElement('select', 'reportPresentationType', array(
				'label' => 'Type:',
				'multiOptions' => Webenq::getReportPresentationTypes(),
				'value' => $rp->type,
			)),
		));
		
		/* display group with options for data collection */
		$this->addDisplayGroup(
			array('useAnswerPossibilityGroup', 'answerPossibilityGroup_id', 'collectionPresentationType', 'filters', 'validators'),
			'collectionPresentation',
			array('legend' => 'Datacollectie')
		);
		
		/* display group with options for data reporting */
		$this->addDisplayGroup(
			array('reportPresentationType'),
			'reportPresentation',
			array('legend' => 'Rapportage')
		);
		
		/* add submit button */
		$this->addElement(
			$this->createElement('submit', 'submit', array(
				'label' => 'opslaan',
			))
		);
		
		if ($qq->answerPossibilityGroup_id) {
			$this->useAnswerPossibilityGroup->setChecked(true);
		} else {
			$this->useAnswerPossibilityGroup->setChecked(false);
		}
	}
	
	public function populate(array $values)
	{
		parent::populate($values);
		
		/* remove selection options if checkbox not checked */
		if (!$this->useAnswerPossibilityGroup->isChecked()) {
			$this->removeElement('answerPossibilityGroup_id');
		}
	}
	
	public function storeValues()
	{
		$qq = $this->_questionnaireQuestion;
		$cp = $qq->CollectionPresentation[0];
		$rp = $qq->ReportPresentation[0];
		
		$values = $this->getValues();
		$qq->fromArray($values);
		
		/* check if the question texts have been modified */
		$isModified = false;
		foreach ($qq->Question->QuestionText as $qt) {
			if (!isset($values['text'][$qt->language])) {
				$isModified = true;
			} elseif ($values['text'][$qt->language] !== $qt->text) {
				$isModified = true;
			}
		}
		
		/**
		 * If text changes are set to be made locally, a copy of the
		 * question is made and assigned to the current questionnaire.
		 */
		if ($isModified) {
			if ($values['text']['change_globally'] == 'local') {
				/* copy question */
				$question = new Question();
				unset($values['text']['change_globally']);
				foreach ($values['text'] as $language => $text) {
					$qt = new QuestionText;
					$qt->text = $text;
					$qt->language = $language;
					$question->QuestionText[] = $qt;
				}
				$question->save();
				$qq->question_id = $question->id;
			} else {
				foreach ($qq->Question->QuestionText as $qt) {
					if (!isset($values['text'][$qt->language])) {
						$qt->delete();
					} else {
						$qt->text = $values['text'][$qt->language];
					}
				}
			}
		}
		
		$cp->type = $values['collectionPresentationType'];
    	$cp->filters = serialize($values['filters']);
    	$cp->validators = serialize($values['validators']);
    	$rp->type = $values['reportPresentationType'];
    	$qq->save();
	}
}