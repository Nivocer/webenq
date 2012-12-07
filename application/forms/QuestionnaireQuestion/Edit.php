<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>, Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_QuestionnaireQuestion_Edit extends Zend_Form
{
    /**
     * Webenq_Model_QuestionnaireQuestion instance
     *
     * @var Webenq_Model_QuestionnaireQuestion $_questionnaireQuestion
     */
    protected $_questionnaireQuestion;

    protected $_rootQuestionsMultiOptions;

    /**
     * Constructor
     *
     * @param Webenq_Model_QuestionnaireQuestion $questionnaireQuestion
     * @param mixed $options
     */
    public function __construct(Webenq_Model_QuestionnaireQuestion $questionnaireQuestion, $options = null)
    {
        $this->_questionnaireQuestion = $questionnaireQuestion;

        // get all questions in this questionnaire at root level
        $rootQuestions = Doctrine_Query::create()
            ->from('Webenq_Model_QuestionnaireQuestion qq')
            ->innerJoin(
                'qq.CollectionPresentation cp WITH qq.questionnaire_id = ?',
                $questionnaireQuestion->questionnaire_id
            )
            ->innerJoin('qq.Question q')
            ->leftJoin('q.QuestionText qt WITH qt.language = ?', Zend_Registry::get('Zend_Locale')->getLanguage())
            ->andWhere('qq.id != ?', $questionnaireQuestion['id'])
            ->andWhere('cp.parent_id IS NULL OR cp.parent_id = 0')
            ->orderBy('cp.page, cp.weight')
            ->execute(null, Doctrine_Core::HYDRATE_ARRAY);
        $rootQuestionsMultiOptions = array(0 => '---');
        foreach ($rootQuestions as $question) {
            $rootQuestionsMultiOptions[$question['CollectionPresentation'][0]['id']] =
                @$question['Question']['QuestionText'][0]['text'];
        }
        $this->_rootQuestionsMultiOptions = $rootQuestionsMultiOptions;

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

        /* add subform for question's general settings */
        $generalForm = new Zend_Form_SubForm();
        $questionEditForm = new Webenq_Form_Question_Edit($qq->Question);
        $generalForm->addSubForm($questionEditForm->getSubForm('text'), 'text');

        if ($qq->existsInMultipleQuestionnaires()) {
            $generalForm->addElement(
                $this->createElement(
                    'radio',
                    'change_globally',
                    array(
                        'label' => 'Hoe wilt u bovenstaande wijzigingen doorvoeren?',
                        'value' => 'local',
                        'multiOptions' => array(
                            'local' => 'Huidige questionnaire',
                            'global' => 'Alle questionnaires',
                        ),
                        'order' => 100,
                    )
                )
            );
        } else {
            $generalForm->addElement(
                $this->createElement(
                    'hidden',
                    'change_globally',
                    array(
                        'value' => 'global',
                    )
                )
            );
        }

        $generalForm->addElement(
            $this->createElement(
                'select',
                'moveTo',
                array(
                    'label' => 'Maak deze vraag een subvraag van:',
                    'multiOptions' => $this->_rootQuestionsMultiOptions,
                    'value' => $cp->parent_id,
                    'order' => 110,
                )
            )
        );

        $generalForm->addElement(
            $this->createElement(
                'submit',
                'submit',
                array(
                    'label' => 'opslaan',
                    'order' => 120,
                )
            )
        );
        $this->addSubForm($generalForm, 'general');

        /* add subform for question's data-collection settings */
        $answerForm = new Zend_Form_SubForm();
        $answerForm->addElements(
            array(
                $this->createElement(
                    'checkbox',
                    'useAnswerPossibilityGroup',
                    array(
                        'label' => 'Maak gebruik van vaste antwoordmogelijkheden:',
                        'checked' => ($qq->answerPossibilityGroup_id) ? true : false,
                    )
                ),
                $this->createElement(
                    'select',
                    'answerPossibilityGroup_id',
                    array(
                        'label' => 'Selecteer een groep met antwoordmogelijkheden:',
                        'multiOptions' => Webenq_Model_AnswerPossibilityGroup::getAll(),
                        'value' => $qq->answerPossibilityGroup_id,
                    )
                ),
                $this->createElement(
                    'select',
                    'collectionPresentationType',
                    array(
                        'label' => 'Type:',
                        'multiOptions' => Webenq::getCollectionPresentationTypes(),
                        'value' => ($qq->answerPossibilityGroup_id) ?
                            $cp->type :
                            Webenq::COLLECTION_PRESENTATION_OPEN_TEXT,
                    )
                ),
                $this->createElement(
                    'submit',
                    'submit',
                    array(
                        'label' => 'opslaan',
                    )
                ),
            )
        );
        $this->addSubForm($answerForm, 'answers');

        /* add subform for question's data-collection settings */
        $validationForm = new Zend_Form_SubForm();
        $validationForm->addElements(
            array(
                $this->createElement(
                    'multiCheckbox',
                    'required',
                    array(
                        'label' => 'Algemeen:',
                        'multiOptions' => array('not_empty' => 'Verplicht'),
                        'value' => unserialize($cp->validators),
                    )
                ),
                $this->createElement(
                    'multiCheckbox',
                    'filters',
                    array(
                        'label' => 'Tekst filters:',
                        'multiOptions' => Webenq::getFilters(),
                        'value' => unserialize($cp->filters),
                    )
                ),
                $this->createElement(
                    'multiCheckbox',
                    'validators',
                    array(
                        'label' => 'Tekst validatie:',
                        'multiOptions' => Webenq::getValidators(),
                        'value' => unserialize($cp->validators),
                    )
                ),
                $this->createElement(
                    'submit',
                    'submit',
                    array(
                        'label' => 'opslaan',
                    )
                ),
            )
        );
        $this->addSubForm($validationForm, 'validation');
    }

    public function isValid($data)
    {
        // check if at least one language is filled out
        $hasAtLeastOneLanguage = false;
        foreach ($data['general']['text'] as $language => $translation) {
            if (trim($translation) != '') {
                $hasAtLeastOneLanguage = true;
                break;
            }
        }

        // disable required setting if at least one language was found
        if ($hasAtLeastOneLanguage) {
            foreach ($this->getSubForm('general')->getSubForm('text')->getElements() as $elm) {
                $elm->setRequired(false);
            }
        }

        return parent::isValid($data);
    }

    public function storeValues()
    {
        $qq = $this->_questionnaireQuestion;
        $cp = $qq->CollectionPresentation[0];

        $values = $this->getValues();

        /* check if the question texts have been modified */
        $isModifiedText = false;
        foreach ($qq->Question->QuestionText as $qt) {
            if (!key_exists($qt->language, $values['general']['text'])) {
                $isModifiedText = true;
                break;
            } elseif ($values['general']['text'][$qt->language] !== $qt->text) {
                $isModifiedText = true;
                break;
            }
        }

        /**
         * If text changes are set to be made locally, a copy of the
         * question is made and assigned to the current questionnaire.
         */
        if ($isModifiedText) {
            if ($values['general']['change_globally'] == 'local') {
                /* copy question */
                $question = new Webenq_Model_Question();
                unset($values['general']['change_globally']);
                foreach ($values['general']['text'] as $language => $text) {
                    $qt = new Webenq_Model_QuestionText;
                    $qt->text = $text;
                    $qt->language = $language;
                    $question->QuestionText[] = $qt;
                }
                $question->save();
                $qq->Question = $question;
            } else {
                // update or delete existing translations
                $texts = $values['general']['text'];
                foreach ($qq->Question->QuestionText as $qt) {
                    if (!key_exists($qt->language, $texts) || empty($texts[$qt->language])) {
                        $qt->delete();
                        unset($texts[$qt->language]);
                    } else {
                        $qt->text = $texts[$qt->language];
                        $qt->save();
                        unset($texts[$qt->language]);
                    }
                }
                // save new translations
                foreach ($texts as $language => $text) {
                    if (!empty($text)) {
                        $qt = new Webenq_Model_QuestionText();
                        $qt->language = $language;
                        $qt->text = $text;
                        $qt->question_id = $qq->question_id;
                        $qt->save();
                    }
                }
            }
        }

        if ($values['answers']['useAnswerPossibilityGroup'] == 1) {
            $qq->answerPossibilityGroup_id = $values['answers']['answerPossibilityGroup_id'];
            $cp->type = $values['answers']['collectionPresentationType'];
        } else {
            $qq->answerPossibilityGroup_id = null;
            $cp->type = $values['answers']['collectionPresentationType'];
            //$cp->type = Webenq::COLLECTION_PRESENTATION_OPEN_TEXT;
        }
        //TODO if collectionPresentationType is null set default type.

        // get filters and validators
        if (!isset($values['validation']['filters'])) $values['validation']['filters'] = array();
        if (!isset($values['validation']['required'])) $values['validation']['required'] = array();
        if (!isset($values['validation']['validators'])) $values['validation']['validators'] = array();
        $filters = $values['validation']['filters'];
        $validators = array_merge($values['validation']['required'], $values['validation']['validators']);

        // get move-to-value
        $cp->parent_id = ($values['general']['moveTo'] > 0) ? $values['general']['moveTo'] : null;

        $cp->filters = serialize($filters);
        $cp->validators = serialize($validators);
        $qq->save();
    }
}