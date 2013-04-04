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
 * @package    WebEnq4
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Base class for our forms, to include some helper functions for arranging
 * form elements
 *
 * @package    WebEnq4
 * @author     Nivocer <webenq@nivocer.com>
 */
class WebEnq4_Form extends Zend_Form
{
    /**
     * List of languages to use for multi-lingual strings
     *
     * @var array Default is set to 'en' and 'nl'
     */
    public $_languages = array('en', 'nl');

    /**
     * Default language to use for multi-lingual strings
     *
     * Note: not dealing with fallback languages or full translation.
     *
     * @var array Default is set to 'en'
     */
    public $_defaultLanguage = 'en';

    /**
     * Constructor
     *
     * Registers form view helper as decorator
     *
     * @param mixed $options
     * @return void
     */
    public function __construct($options = null)
    {
        $this->addPrefixPath('WebEnq4_Form_', 'WebEnq4/Form/');
        $this->addPrefixPath('ZendX_JQuery_Form_', 'ZendX/JQuery/Form/');

        parent::__construct($options);
    }

    /**
     * Add a set of checkboxes with options to a form in a display group
     *
     * @param array Array with the name for the options, the group legend
     * @param array The options to present as checkboxes, with their info
     * @todo Move this to a more generic place, WebEnq4 library for instance
     * @return void
     */
    public function addCheckboxOptions($group, $options)
    {
        $list = array();
        foreach ($options as $item => $info) {
            $v = new Zend_Form_Element_Checkbox($item);
            $v->setBelongsTo($group['name']);
            if (isset($info['label'])) {
                $v->setLabel($info['label']);
            } else {
                $v->setLabel($item);
            }
            $v->getDecorator('Label')->setOption('placement', 'append');
            $this->addElement($v);

            $list[] = $v->getName();
        }
        if (count($list) > 0) {
            $this->addDisplayGroup(
                $list,
                $group['name'],
                array(
                    'class' => 'optionlist',
                    'legend' => $group['legend']
                )
            );
        }
    }

    /**
     * Decorate group or element as table cell or table header cell
     *
     * @param Form_Element Form element to decorate
     * @param boolean Whether to decorate as table header cell (default: false)
     * @return Form_Element
     */
    public function decorateAsTableCell($element, $header = false) {
        $tag = ($header ? 'th' : 'td');
        $element->removeDecorator('DtDdWrapper');
        $element->removeDecorator('Label');
        $element->addDecorator('Tooltip');
        $element->addDecorator('HtmlTag', array('tag' => $tag));
        return $element;
    }

    /**
     * Decorate group or element as table row
     *
     * @param Form_Element Form element to decorate
     * @return Form_Element
     */
    public function decorateAsTableRow($element) {
        $element->removeDecorator('Fieldset');
        $element->removeDecorator('HtmlTag');
        $element->removeDecorator('DtDdWrapper');
        $element->removeDecorator('Label');
        $element->addDecorator('HtmlTag', array('tag' => 'tr'));
        return $element;
    }

    /**
     * See if there are Translations, swap field name and language and add
     * as new array elements, to allow easier inclusion via Zend Form elements.
     *
     * So if <code>$defaults['Translation']['en']['text']</code> is available,
     * this will make <code>$defaults['text']['en']</code> available too.
     *
     * It is assumed that the defaults come from an object toArray() call:
     *
     * <ul>
     * <li>translated fields don't exist as object properties
     * <li>no additional defaults are added that conflict with field names
     * </ul>
     *
     * @see Zend_Form::setDefaults()
     * @see getValues() for the converse operation
     */
    public function setDefaults(array $defaults)
    {
        if (isset($defaults['Translation'])) {
            foreach ($defaults['Translation'] as $lang => $record) {
                foreach ($record as $field => $value) {
                    if (($field != 'id') && ($field != 'lang')) {
                        $defaults[$field][$lang] = $value;
                    }
                }
            }
        }

        parent::setDefaults($defaults);
    }

    /**
     * Check if either cancel is clicked or all validators succeed
     *
     * @param array $values
     * @return boolean
     * @see Zend_Form::isValid()
     */
    public function isValid($values)
    {
        if ($this->isCancelled($values)) {
            return true;
        } else {
            $result = parent::isValid($values);

            return $result;
        }
    }

    /**
     * Check if the cancel button was submitted
     *
     * @param array $values
     * @return boolean
     */
    public function isCancelled($values)
    {
        return (isset($values['cancel']));
    }
}