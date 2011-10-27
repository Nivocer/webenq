<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_Questionnaire_Edit extends Webenq_Form_Questionnaire_Add
{
    /**
     * Questionnaire instance
     *
     * @var array $questionnaire
     */
    protected $_questionnaire;

    /**
     * Constructor
     *
     * @param Questionnaire $questionnaire
     * @param mixed $options
     */
    public function __construct(Webenq_Model_Questionnaire $questionnaire, $options = null)
    {
        $this->_questionnaire = $questionnaire;
        parent::__construct($options);
    }

    /**
     * Initialises the form
     *
     * @return void
     */
    public function init()
    {
        $this->setName(get_class($this));
//        $this->addElements(array(
//            $this->createElement('hidden', 'id'),
//        ));
        parent::init();
        $this->setDefaults($this->_questionnaire->toArray());
    }

    public function setDefaults(array $values)
    {
        if (isset($values['QuestionnaireTitle'])) {
            foreach ($values['QuestionnaireTitle'] as $translation) {
                $this->getElement($translation['language'])->setValue($translation['text']);
            }
        }
    }
}