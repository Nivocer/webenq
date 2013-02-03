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
 * @author     Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_Category_Add extends Zend_Form
{
    public function init()
    {
        foreach (Webenq_Language::getLanguages() as $language) {
            $this->addElement(
                $this->createElement(
                    'text',
                    $language,
                    array(
                        'belongsTo' => 'text',
                        'label' => t('title') . " ($language)",
                        'filters' => array('StringTrim'),
                        'validators' => array(
                            new Zend_Validate_NotEmpty()
                        ),
                    )
                )
            );
        }
        /*$this->addElement(
            $this->createElement(
                'radio',
                'default_language',
                array(
                    'label' => 'Default language',
                    'required' => false,
                    'value' => Zend_Registry::get('Zend_Locale')->getLanguage(),
                    'multiOptions' => Webenq_Language::getLanguages(),

                )
            )
        );

*/
        $this->addElement(
            $this->createElement(
                'checkbox',
                'active',
                array(
                    'label' => 'Active',
                    'checked' => (isset($this->_category->active)&& $this->_category->active) ? true : false,
                )
            )
        );

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

    public function isValid($values)
    {

        // at least one language is required
        $hasAtLeastOneLanguage = false;
        if (isset($values['text'])) {
            foreach ($values['text'] as $language) {
                if (!empty($language)) {
                    $hasAtLeastOneLanguage = true;
                    break;
                }
            }
        }
        if (!$hasAtLeastOneLanguage) {
            $elements = $this->getElements();
            $firstElement = array_shift($elements);
            $firstElement->setRequired();
        }

        return parent::isValid($values);
    }
}