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

        $category = new Zend_Form_Element_Select('category_id');
        $category->setLabel('Category');
        $categories = Webenq_Model_Category::getCategories();
        foreach ($categories as $option) {
            $category->addMultiOption($option->get('id'), $option->getCategoryText()->text);
        }
        $this->addElement($category);

        $active = new Zend_Form_Element_Checkbox('active');
        $active->setLabel('Active');
        $this->addElement($active);

//        $datetime_start = new WebEnq4_Form_Element_DateTimePicker('date_start');
        $datetime_start = new ZendX_JQuery_Form_Element_DatePicker('date_start');
        $datetime_start->setLabel('Publish from');
        $this->addElement($datetime_start);

//        $datetime_end = new WebEnq4_Form_Element_DateTimePicker('date_end');
        $datetime_end = new ZendX_JQuery_Form_Element_DatePicker('date_end');
        $datetime_end->setLabel('Publish until');
        $this->addElement($datetime_end);

        $this->addElement(
            'submit',
            'cancel',
            array(
                'label' => 'cancel',
            )
        );

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
        if ($this->isCancelled($values)) {
            return true;
        } else {
            return parent::isValid($values);
        }
    }

    public function isCancelled($values)
    {
        return (isset($values['cancel']));
    }
}