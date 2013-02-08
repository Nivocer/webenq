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
 * @package    Webenq
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * @package    Webenq
 */
class Webenq_Form_Confirm extends Zend_Form
{
    /**
     * Id of the record
     *
     * @var int $_id
     */
    protected $_id;

    /**
     * Text to display
     *
     * @var string $_text
     */
    protected $_text;

    /**
     * Constructor
     *
     * Sets the id and text to display and calls parent constructor
     *
     * @param int $id
     * @param string $text
     * @param mixed $options
     * @return void
     * @todo remove this function, it redefines the constructor signature...
     */
    public function __construct($id = '', $text = '', $options = null)
    {
        $this->_id = $id;
        $this->_text = $text;
        parent::__construct($options);
    }

    /**
     * Initialises the form
     *
     * @return void
     */
    public function init()
    {
        $this->addElements(
            array(
                $this->createElement(
                    'hidden',
                    'id', array(
                        'value' => $this->_id,
                        'label' => $this->_text,
                    )
                ),
                $this->createElement('submit', 'yes', array('label' => 'yes')),
                $this->createElement('submit', 'no', array('label' => 'no')),
            )
        );
    }

    /**
     * Sets the id and text to display in the confirmation dialog
     *
     * @param int $id
     * @param string $text
     * @return void
     */
    public function setConfirmation($id = '', $text = '')
    {
        $this->getElement('id')->setLabel($text);
        $this->getElement('id')->setValue($id);
    }

    /**
     * Check if the cancel button was submitted
     *
     * @param array $values
     * @return boolean
     */
    public function isCancelled($values)
    {
        return (isset($values['no']));
    }
}