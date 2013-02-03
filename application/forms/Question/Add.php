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
 * @author     Bart Huttinga <b.huttinga@nivocer.com>, Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_Question_Add extends Zend_Form
{
    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        $text = new Zend_Form_SubForm();
        $text->setDecorators(array('FormElements'));
        $this->addSubForm($text, 'text');

        $languages = Webenq_Language::getLanguages();
        foreach ($languages as $language) {
            $text->addElement(
                $this->createElement(
                    'text',
                    $language,
                    array(
                        'label' => t('text') . ' (' . $language . '):',
                        'size' => 60,
                        'maxlength' => 255,
                        'autocomplete' => 'off',
                        'required' => true,
                        'validators' => array(
                            new Zend_Validate_NotEmpty(),
                        ),
                    )
                )
            );
        }

        $this->addElement(
            $this->createElement(
                'submit',
                'submit',
                array(
                    'label' => 'save',
                )
            )
        );
    }

    public function isValid($data)
    {
        // check if at least one language is filled out
        $hasAtLeastOneLanguage = false;
        foreach ($data['text'] as $language => $translation) {
            if (trim($translation) != '') {
                $hasAtLeastOneLanguage = true;
                break;
            }
        }

        // disable required setting if at least one language was found
        if ($hasAtLeastOneLanguage) {
            foreach ($this->getSubForm('text')->getElements() as $elm) {
                $elm->setRequired(false);
            }
        }

        return parent::isValid($data);
    }
}