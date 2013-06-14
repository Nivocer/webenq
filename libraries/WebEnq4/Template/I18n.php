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
 * @package    WebEnq4_I18n
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Add multilingual capabilities to your Doctrine models.
 *
 * You will need to define a class in your schema like this:
 *
 * <code>
 * Text:
 *   actAs:
 *     I18n:
 *       fields: [text]
 *   columns:
 *     text: string(255)
 * </code>
 *
 * Then, you can internationalise your own classes, and all texts will be
 * stored in via this class.
 *
 * Optionally, you can subclass this table, to create separate sets of texts
 * for different purposes, while inheriting the functionality to handle this.
 *
 * <code>
 * Title:
 *   inheritance:
 *     extends: Text
 *
 * Questionnaire:
 *   actAs:
 *     WebEnq4_Template_I18n:
 *       fields: title
 *       class: Title
 *   columns:
 *     title: string
 *     active: boolean
 * </code>
 *
 * The option `class:` is not required, and will default to `Text`.
 *
 * **Nota bene**: currently, the full class name `Title` that will be used is
 * hard-coded to `Webenq_Model_Title`.
 *
 * Based on the Doctrine I18n behavior, adapted to support a single language
 * table that supports re-use of translations.
 *
 * @package     WebEnq4_I18n
 * @author      Rolf Kleef <r.kleef@nivocer.com>
 */
class WebEnq4_Template_I18n extends Doctrine_Template
{
    /**
     * __construct
     *
     * @param string $array
     * @return void
     */
    public function __construct(array $options = array())
    {
	    parent::__construct($options);
        $this->_plugin = new WebEnq4_Doctrine_I18n($this->_options);
    }

    /**
     * Initialize the I18n plugin for the template
     *
     * @return void
     */
    public function setUp()
    {
        $this->_plugin->initialize($this->_table);
        $this->addListener(new WebEnq4_Doctrine_Listener_I18n($this->_options));
    }

    /**
     * Get the plugin instance for the I18n template
     *
     * @return void
     */
    public function getI18n()
    {
        return $this->_plugin;
    }

    /**
     * Get localized field string
     *
     * if $language not set, use current language
     * first try to return string in requested language
     * if not succeeded, try to return string language in the first preferredLanguage
     * if not succeeded, try to return string in any language
     * if not succeeded, return empty string
     *
     * @param string $field
     * @param string $language
     * @return string
     */
    public function getTranslation($field='text', $language = null)
    {
        // get curren language if not given
        $invoker=$this->getInvoker();
        if (!$language) {
            $language = Zend_Registry::get('Zend_Locale')->getLanguage();
        }

        if (isset($invoker->Translation[$language]) && isset($invoker->Translation[$language]->$field)) {
           return $invoker->Translation[$language]->$field;
        }

        // return the first preferred language that is set
        $preferredLanguages = Zend_Registry::get('preferredLanguages');
        foreach ($preferredLanguages as $lang) {
            if (isset($invoker->Translation[$lang]) && isset($invoker->Translation[$lang]->$field)) {
                return $invoker->Translation[$lang]->$field;
            }
        }

        // return any found language
        if (count($invoker->Translation) > 0 && isset($invoker->Translation[0]->$field)) {
            return $invoker->Translation[0]->$field;
        }

        // nothing, return empty string
        return '';
    }

    /**
     * set translations from Array (eg postdata)
     *
     * @param  $array array $array[$field][$language]=$value;
     */
    public function setTranslationFromArray($array){

        if (is_array($array)) {
            $invoker=$this->getInvoker();
            $columns=$invoker->Translation->getTable()->getColumnNames();
            foreach ($array as $field => $values) {
                if (is_array($values)) {
                    if (in_array($field, $columns) && !in_array($field, array('id', 'lang'))){
                        foreach ($values as $language =>$value) {
                            $invoker->Translation[$language]->$field=$value;
                        }
                    }
                }
            }

        }
    }


}
