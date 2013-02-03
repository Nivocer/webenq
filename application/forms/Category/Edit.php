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
class Webenq_Form_Category_Edit extends Webenq_Form_Category_Add
{
    /**
     * Category instance
     *
     * @var array $category
     */
    protected $_category;

    /**
     * Constructor
     *
     * @param Category $category
     * @param mixed $options
     */
    public function __construct(Webenq_Model_Category $category, $options = null)
    {
        $this->_category = $category;
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
        $this->setDefaults($this->_category->toArray());
    }

    public function setDefaults(array $values)
    {
        if (isset($values['CategoryText'])) {
            foreach ($values['CategoryText'] as $translation) {
                $this->getElement($translation['language'])->setValue($translation['text']);
            }
        }
    }
}