<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_AnswerPossibility_Edit extends Zend_Form
{
    /**
     * Current answer-possibility
     *
     * @var Webenq_Model_AnswerPossibility $_answerPossibility
     */
    protected $_answerPossibility;

    /**
     * All answer-possibilities in current group
     *
     * @var array $_answerPossibilities
     */
    protected $_answerPossibilities;

    /**
     * Array of answer-possibility-groups
     *
     * @var array $_answerPossibilityGroups
     */
    protected $_answerPossibilityGroups;

    /**
     * Class constructor
     *
     * @param Webenq_Model_AnswerPossibility $answerPossibility
     * @param string $language
     * @param array|Zend_Config $options
     * @return void
     */
    public function __construct(Webenq_Model_AnswerPossibility $answerPossibility, $language, array $options = null)
    {
        $this->_answerPossibility = $answerPossibility;

        $groups = Doctrine_Query::create()
            ->from('Webenq_Model_AnswerPossibilityGroup apg')
            ->orderBy('apg.name')
            ->execute();
        foreach ($groups as $group) {
            $this->_answerPossibilityGroups[$group->id] = $group->name;
        }

        $possibilities = Doctrine_Query::create()
            ->from('Webenq_Model_AnswerPossibility ap')
            ->innerJoin('ap.AnswerPossibilityText apt WITH apt.language = ?', $language)
            ->where('ap.answerPossibilityGroup_id = ?', $answerPossibility->answerPossibilityGroup_id)
            ->andWhere('ap.id != ?', $answerPossibility->id)
            ->orderBy('ap.value, apt.text')
            ->execute();
        $this->_answerPossibilities = array('--- selecteer ---');
        foreach ($possibilities as $possibility) {
            $this->_answerPossibilities[$possibility->id] = $possibility->AnswerPossibilityText[0]->text;
        }

        parent::__construct($options);
    }

    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        $this->addElements(array(
            $this->createElement('hidden', 'id', array(
                'value' => $this->_answerPossibility->id,
            )),
            $this->createElement('select', 'language', array(
                'label' => 'Taal:',
                'multiOptions' => array(
                    'nl' => 'nl',
                ),
                'value' => $this->_answerPossibility->AnswerPossibilityText[0]->language,
            )),
            $this->createElement('text', 'text', array(
                'label' => 'Tekst:',
                'value' => $this->_answerPossibility->AnswerPossibilityText[0]->text,
                'required' => true,
            )),
            $this->createElement('text', 'value', array(
                'label' => 'Waarde:',
                'value' => $this->_answerPossibility->value,
                'required' => true,
                'validators' => array(
                    'Int',
                ),
            )),
            $this->createElement('select', 'answerPossibilityGroup_id', array(
                'label' => 'Groep:',
                'value' => $this->_answerPossibility->answerPossibilityGroup_id,
                'multiOptions' => $this->_answerPossibilityGroups,
            )),
            $this->createElement('submit', 'submitedit', array(
                'label' => 'opslaan',
            )),
            $this->createElement('select', 'answerPossibility_id', array(
                'label' => 'Antwoordmogelijkheden:',
                'multiOptions' => $this->_answerPossibilities,
            )),
            $this->createElement('submit', 'submitmove', array(
                'label' => 'verplaatsen',
            )),
            $this->createElement('submit', 'submitnull', array(
                'label' => 'nul-waarde van maken',
            )),
        ));

        $this->addDisplayGroup(
            array('language', 'value', 'answerPossibilityGroup_id', 'text', 'submitedit'),
            'edit',
            array('legend' => 'Bewerken')
        );
        $this->addDisplayGroup(
            array('answerPossibility_id', 'submitmove'),
            'move',
            array('legend' => 'Verplaatsen')
        );
        $this->addDisplayGroup(
            array('submitnull'),
            'null',
            array('legend' => 'Markeren als nul-waarde')
        );
    }

    public function isValid($values)
    {
        if (key_exists('submitmove', $values) || key_exists('submitnull', $values)) {
            foreach ($this->edit->getElements() as $elm) {
                $elm->setRequired(false);
            }
        }

        return parent::isValid($values);
    }
}