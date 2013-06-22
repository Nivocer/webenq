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
 * @package    Webenq_Models
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Answer domain class definition
 *
 * @package    Webenq_Models
 * @author     Jaap-Andre de Hoop <j.dehoop@nivocer.com>, Rolf Kleef <r.kleef@nivocer.com>
 */
class Webenq_Model_AnswerDomain extends Webenq_Model_Base_AnswerDomain
{
    public static function getAll(){
        $answerDomain=Doctrine_Core::getTable('Webenq_Model_AnswerDomain')->findAll();
        return $answerDomain;
    }

    /**
     * Return the available answer domain types
     *
     * @return Array List of available answer domain types
     */

    public static function getAvailableTypes()
    {
        return array(
            'AnswerDomainChoice' => array(
                'label' => 'Choice'
            ),
            'AnswerDomainNumeric' => array(
                'label' => 'Numeric'
            ),
            'AnswerDomainText' => array(
                'label' => 'Text'
            )
        );
    }
//     public static function getAvailablePrestentationMethods(){
//         return array(
//             t('Zend_Form_Element_Textarea'),
//             t('Zend_Form_Element_Text'),
//             t('ZendX_JQuery_Form_Element_AutoComplete'),

//             t('Zend_Form_Element_File'),
//             t('ZendX_JQuery_Form_Element_Slider'),
//             t('Zend_Form_Element_Radio'),
//             t('Zend_Form_Element_Select'),
//             t('Zend_Form_Element_MultiCheckbox'),
//             t('Zend_Form_Element_Checkbox'),
//             t('Zend_Form_Element_Image'),
//         );
//     }

    /**
     * Return the available presentation formats
     *
     * @return Array List of available presentation formats
     */
    public static function getAvailablePresentations()
    {
        return array(
            'Text' => array(
                'label' => 'Present as text',
                'element'=> 'WebEnq4_Form_Element_Note'
            )
        );
    }

    /**
     * Return the available validators for the answer domain type.
     *
     * @return Array List of available validators
     */
    public static function getAvailableValidators()
    {
        return array();
    }

    /**
     * Return the available filters for the answer domain type.
     *
     * @return Array List of available filters
     */
    public static function getAvailableFilters()
    {
        return array();
    }

    /**
     * Fills array with object properties, and adds translations
     *
     * @param bool $deep
     * @param bool $prefixKey Not used
     * @return array
     * @see Doctrine_Record::fromArray()
     */
    public function toArray($deep = true, $prefixKey = false)
    {
        $result = parent::toArray($deep, $prefixKey);

        // @todo We should find a way to do this via the I18n behavior, of find out why 'deep=true' doesn't do this
        $result['Translation'] = $this->Translation->toArray();

        return $result;
    }
    /**
     * Imports data from a php array
     *
     * @param string $array  array of data, see link for documentation
     * @param bool   $deep   whether or not to act on relations
     * @return void
     * @see Doctrine_Record::fromArray()
     */
    public function fromArray(array $array, $deep = true)
    {
        if ($deep) {
            // @todo We should find a way to do this via the I18n behavior, of find out why 'deep=true' doesn't do this
            $this->setTranslationFromArray($array);
        }
        parent::fromArray($array, $deep);
    }


}