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
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Form_ReportDefinition extends Zend_Form
{
    /**
     * Questions
     */
    protected $_questions = array();


    /**
     * Questions
     */
    protected $_outputFormats = array();


    /**
     * Questions
     */
    protected $_reportTypes = array();


    /**
     * Languages
     */
    protected $_languages = array();


    /**
     * Customers
     */
    protected $_customers = array();


    /**
     * Pages
     */
    protected $_pages = array();


    /**
     * Class constructor
     *
     * @param array $questions
     * @param array $outputFormats
     * @param array $reportTypes
     * @param array $languages
     * @param array $customers
     * @param array $pages
     * @param array $options Zend_Form options
     * @return void
     */
    public function __construct(array $questions, array $outputFormats, array $reportTypes, array $languages,
        array $customers, array $pages, $options = null)
    {
        parent::__construct($options);

        $this->_questions = $questions;
        $this->_outputFormats = $outputFormats;
        $this->_reportTypes = $reportTypes;
        $this->_languages = $languages;
        $this->_customers = $customers;
        $this->_pages = $pages;

        $this->_buildForm();
    }


    /**
     * Builds the form
     */
    protected function _buildForm()
    {
        /* needed to show the default checked radio button in FireFox */
        $this->setAttrib("autocomplete", "off");

        $filenameFilter = new Zend_Filter_PregReplace("#[^A-Za-z0-9_-]#", "_");

        $filename = new Zend_Form_Element_Text('output_filename');
        $filename->setLabel('File name for report (without extension)')
            ->addFilter($filenameFilter);

        $output = new Zend_Form_Element_Radio('output_format');
        $output
            ->setLabel('Select an output format:')
            ->setMultiOptions($this->_outputFormats)
            ->setRequired(true);

        $report = new Zend_Form_Element_Radio('report_type');
        $report
            ->setLabel('Select a report type')
            ->setMultiOptions($this->_reportTypes)
            ->setRequired(true);

        $language = new Zend_Form_Element_Radio('language');
        $language
            ->setLabel('Select a language')
            ->setMultiOptions($this->_languages)
            ->setRequired(true);

        $customer = new Zend_Form_Element_Radio('customer');
        $customer
            ->setLabel('Select a customer')
            ->setMultiOptions($this->_customers)
            ->setRequired(true);

        $page = new Zend_Form_Element_Radio('page');
        $page
            ->setLabel('Select a page type')
            ->setMultiOptions($this->_pages)
            ->setRequired(true);

        $group = new Zend_Form_Element_Select('group_question_id');
        $group
            ->setLabel('Select a question to group the data')
            ->setRequired(false)
            ->setMultiOptions(array('' => '--- no group ---'))
            ->addMultiOptions($this->_questions);

        $split = new Zend_Form_Element_Select('split_question_id');
        $split
            ->setLabel('Select a question to splits the data')
            ->setRequired(false)
            ->setMultiOptions(array('' => '--- no splits ---'))
            ->addMultiOptions($this->_questions);

        $ignore = new Zend_Form_Element_MultiCheckbox('ignore_question_ids');
        $ignore
            ->setLabel('Select the questions we don\'t want to report about')
            ->setRequired(false)
            ->addMultiOptions($this->_questions);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Submit');

        $this->addElements(
            array(
                $filename, $output, $report, $language, $customer, $page, $group, $split, $ignore, $submit
            )
        );
    }
}