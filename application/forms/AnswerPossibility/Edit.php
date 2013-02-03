<?php
/**
 * WebEnq4
 *
 *  LICENSE
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    Webenq_Questionnaires_Manage
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Form class
 *
 * @package    Webenq_Questionnaires_Manage
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_AnswerPossibility_Edit extends ZendX_JQuery_Form
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
        $this->_answerPossibilities = array('' => '--- selecteer ---');
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
        $this->setAttrib('id', 'mainForm')->setDecorators(
            array(
                'FormElements',
                array(
                    'TabContainer',
                    array(
                        'class' => 'tabs'
                    )
                ),
            'Form',
            )
        );

        $this->addSubForm($this->_getEditSubform(), 'edit');
        $this->addSubForm($this->_getSynonymSubform(), 'synonym');
        $this->addSubForm($this->_getNullValueSubform(), 'nullvalue');
    }

    public function isValid($values)
    {
        if (key_exists('submitmove', $values) || key_exists('submitnull', $values)) {
            foreach ($this->edit->getElements() as $elm) {
                $elm->setRequired(false);
            }
        }

        if (key_exists('submitedit', $values) || key_exists('submitnull', $values)) {
            foreach ($this->synonym->getElements() as $elm) {
                $elm->setRequired(false);
            }
        }

        return parent::isValid($values);
    }

    protected function _getEditSubForm()
    {
        $form = new ZendX_JQuery_Form(
            array(
                'decorators' => array(
                    'FormElements',
                    array(
                        'HtmlTag',
                        array(
                            'tag' => 'dl'
                        )
                    ),
                    array(
                        'TabPane', array(
                            'jQueryParams' => array(
                                'containerId' => 'mainForm',
                                'title' => 'Edit',
                             )
                        )
                    ),
                ),
            )
        );

        foreach (Webenq_Language::getLanguages() as $language) {
            $form->addElement(
                $this->createElement(
                    'text',
                    $language,
                    array(
                        'label' => t('text') . " ($language)",
                        'size' => 60,
                        'maxlength' => 255,
                        'autocomplete' => 'off',
                        'value' => $this->_answerPossibility->getAnswerPossibilityText($language)->text,
                    )
                )
            );
        }

        $form->addElements(
            array(
                $this->createElement(
                    'text',
                    'value',
                    array(
                        'label' => 'value',
                        'value' => $this->_answerPossibility->value,
                        'required' => true,
                        'validators' => array('Int'),
                    )
                ),
                $this->createElement(
                    'select',
                    'answerPossibilityGroup_id',
                    array(
                        'label' => 'group',
                        'value' => $this->_answerPossibility->answerPossibilityGroup_id,
                        'multiOptions' => $this->_answerPossibilityGroups,
                    )
                ),
                $this->createElement(
                    'submit',
                    'submitedit',
                    array(
                        'label' => 'save',
                    )
                ),
            )
        );

//        foreach ($form->getElements() as $element) {
//            $element->setBelongsTo('edit');
//        }

        return $form;
    }

    protected function _getSynonymSubForm()
    {
        $form = new ZendX_JQuery_Form(
            array(
                'decorators' => array(
                    'FormElements',
                    array(
                        'HtmlTag', array(
                            'tag' => 'dl'
                        )
                    ),
                    array(
                        'TabPane',
                        array(
                            'jQueryParams' => array(
                                'containerId' => 'mainForm',
                                'title' => 'Synonym',
                            )
                        )
                    ),
                ),
            )
        );

        $form->addElements(
            array(
                $this->createElement(
                    'select',
                    'answerPossibility_id',
                    array(
                        'label' => 'answer possibilities',
                        'required' => true,
                        'multiOptions' => $this->_answerPossibilities,
                    )
                ),
                $this->createElement(
                    'submit',
                    'submitmove',
                    array(
                        'label' => 'move',
                    )
                ),
            )
        );

        return $form;
    }

    protected function _getNullValueSubForm()
    {
        $form = new ZendX_JQuery_Form(
            array(
                'decorators' => array(
                    'FormElements',
                    array(
                        'HtmlTag', array(
                            'tag' => 'dl'
                        )
                    ),
                    array(
                        'TabPane',
                        array(
                            'jQueryParams' => array(
                                'containerId' => 'mainForm',
                                'title' => 'Null value',
                            )
                        )
                    ),
                ),
            )
        );

        $form->addElements(
            array(
                $this->createElement(
                    'submit',
                    'submitnull',
                    array(
                        'label' => 'make null-value',
                    )
                ),
            )
        );

//        foreach ($form->getElements() as $element) {
//            $element->setBelongsTo('nullvalue');
//        }

        return $form;
    }
}