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
 * @package    Webenq_Reports_Manage
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Form class
 *
 * @package    Webenq_Reports
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_ReportGeneration_Index extends Zend_Form
{
    protected $_subDirs = array();

    public function __construct($subDirs, $options = null)
    {
        $this->_subDirs = $subDirs;
        parent::__construct($options);
    }

    public function init()
    {
        $createDir = new Zend_Form_Element_Text('createDir');
        $createDir->setLabel('Create a new directory');

        $selectDir = new Zend_Form_Element_Select('selectDir');
        $selectDir->setLabel('Select an existing directory')
            ->setMultiOptions(array('' => ''))
            ->addMultiOptions($this->_subDirs);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('generate reports');

        $this->addElements(array($createDir, $selectDir, $submit));
    }

    public function isValid($data)
    {
        if (!$data['createDir'] && !$data['selectDir']) {
            $this->getElement('selectDir')->addError('Select a directory');
            return false;
        }

        if ($data['createDir'] && $data['selectDir']) {
            $this->getElement('selectDir')->addError('Select just one directory');
            return false;
        }

        return parent::isValid($data);
    }
}