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
 * @link        www.doctrine-project.org
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
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
}
