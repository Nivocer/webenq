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
class Webenq_Form_Question_Edit extends Webenq_Form_Question_Add
{
    /**
     * Current question
     *
     * @var Question $_question
     */
    protected $_question;

    /**
     * Class constructor
     *
     */
    public function __construct(Webenq_Model_Question $question, $options = null)
    {
        $this->_question = $question;
        parent::__construct($options);
        $this->populate(array());
    }

    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $id = $this->createElement(
            'hidden',
            'id',
            array(
                'value' => $this->_question->id,
            )
        );
        $this->addElements(array($id));
    }

    /**
     * Populates the form
     *
     * @param array $values
     * @return void
     */
    public function populate(array $values)
    {
        foreach ($this->_question->QuestionText as $questionText) {
            $language = $questionText->language;
            $this->text->$language->setValue($questionText->text);
        }
        parent::populate($values);
    }
}