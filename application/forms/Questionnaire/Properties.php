<?php
/**
 * Form to add or edit questionnaire properties
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>,
 *              Jaap-Andre de Hoop <j.dehoop@nivocer.com>,
 *              Rolf Kleef <r.kleef@nivocer.com>
 */
class Webenq_Form_Questionnaire_Properties extends Zend_Form
{
    public function init()
    {
        $title = new WebEnq4_Form_Element_MlTextDefaultLanguage('title');
        $title->setLabel('Title');
        $title->setRequired();
        $title->setAttrib('languages', Webenq_Language::getLanguages());
        // @todo move external dependency on languages into controller/elsewhere
        $this->addElement($title);

/*        $datetime_start = new WebEnq4_Form_Element_DateTimePicker('date_start');
        $this->addElement($datetime_start);

        $date_start = new ZendX_JQuery_Form_Element_DatePicker('start_date');
        $date_start->setLabel('Active from');
        $date_start->addValidator(new Zend_Validate_Date('YYYY-MM-DD'));
        $date_start->setJqueryParams(array('dateFormat'=>'yy-m-d', 'showWeek'=>true));
        $this->addElement($date_start);

        $time_start = new Zend_Form_Element_Text('start_time');
        $time_start->setLabel('Time');
        $time_start->setAttrib('size',25);
        $this->addElement($time_start);

        $date_end = new ZendX_JQuery_Form_Element_DatePicker('date_end');
        $date_end->setLabel('Active until');
        $date_end->addValidator(new Zend_Validate_Date('DD-MM-YYYY'));
        $date_end->setAttrib('size',25);
        $this->addElement($date_end);
*/
        $this->addElement(
            'submit',
            'submit',
            array(
                'label' => 'save',
            )
        );
    }

    public function isValid($values)
    {
        return parent::isValid($values);
    }
}