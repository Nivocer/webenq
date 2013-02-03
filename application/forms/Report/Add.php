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
 * @package    Webenq_Reports_Manage
 * @author     Bart Huttinga <b.huttinga@nivocer.com>, Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_Report_Add extends Zend_Form
{
    /**
     * Builds the form
     *
     * @return void
     */
    public function init()
    {
        $title = new Zend_Form_SubForm(array('legend' => 'report title'));
        $this->addSubForm($title, 'title');

        $languages = Webenq_Language::getLanguages();
        foreach ($languages as $language) {
            $title->addElement(
                $this->createElement(
                    'text',
                    $language,
                    array(
                        'label' => $language . ':',
                        'size' => 60,
                        'maxlength' => 255,
                        'autocomplete' => 'off',
                        'required' => true,
                        'filters' => array('StringTrim'),
                        'validators' => array('NotEmpty'),
                    )
                )
            );
        }

        $this->addElement(
            $this->createElement(
                'select',
                'questionnaire_id',
                array(
                    'label' => 'questionnaire',
                    'required' => true,
                    'multiOptions' => Webenq_Model_Questionnaire::getKeyValuePairs(),
                )
            )
        );

        $this->addElement(
            $this->createElement(
                'select',
                'language',
                array(
                    'label' => 'language',
                    'required' => true,
                    'multiOptions' => $languages,
                )
            )
        );

        $this->addElement(
            $this->createElement(
                'select',
                'customer',
                array(
                    'label' => 'customer',
                    'required' => true,
                    'multiOptions' => array(
                        'default'=>'Other',
                        'departmentB' => 'departmentB',
                        'departmentC'=>'departmentC'
                    ),
                )
            )
        );


        $this->addElement(
            $this->createElement(
                'text',
                'output_dir',
                array(
                    'label' => "output subdirectory",
                    'size' => 60,
                    'maxlength' => 255,
                    'autocomplete' => 'off',
                    'required' => false,
                    'filters' => array('StringTrim'),
                    //'validators' => array('NotEmpty'),
                )
            )
        );


        $this->addElement(
            $this->createElement(
                'text',
                'output_name',
                array(
                    'label' => 'output file name',
                    'required' => true,
                )
            )
        );

        $this->addElement(
            $this->createElement(
                'select',
                'output_format',
                array(
                    'label' => 'output format',
                    'required' => true,
                    'multiOptions' => array(
                        'pdf'	=> 'pdf',
                        'doc'	=> 'doc',
                        'odt'	=> 'odt',
                        'rtf'	=> 'rtf',
                        'docx'	=> 'docx (untested)',
                        'html'	=> 'html',
                        'xml'	=> 'xml',
                        'xls'	=> 'xls',
                        'jxl'	=> 'jxl (untested)',
                        'xlsx'	=> 'xlsx (untested)',
                        'print'	=> 'print',
                    )
                )
            )
        );

        $this->addElement(
            $this->createElement(
                'select',
                'orientation',
                array(
                    'label' => 'orientation',
                    'required' => true,
                    'multiOptions' => array(
                        'a'	=> 'automatic',
                        'p'	=> 'portrait',
                        'l'	=> 'landscape',
                    )
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

    public function isValid($data)
    {
        // disable required setting if at least one language is set
        foreach ($data['title'] as $language => $translation) {
            if ($translation) {
                foreach ($this->getSubForm('title')->getElements() as $elm) {
                    $elm->setRequired(false);
                }
                break;
            }
        }

        return parent::isValid($data);
    }
}