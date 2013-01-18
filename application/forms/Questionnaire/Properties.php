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
    const ERR_END_IS_BEFORE_START = 'endDateIsBeforeStartDate';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::ERR_END_IS_BEFORE_START => "The end date should be after the start date",
    );

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

        $date_start = new WebEnq4_Form_Element_DateTimePicker('date_start');
        $date_start->setLabel('Publish from');
        $this->addElement($date_start);

        $date_end = new WebEnq4_Form_Element_DateTimePicker('date_end');
        $date_end->setLabel('Publish until');
        $this->addElement($date_end);

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
            $result = parent::isValid($values);

            if (isset($values["date_start"])
                    && isset($values["date_end"])
                    && ($values["date_start"]!=='')
                    && ($values["date_end"]!=='')) {
                if (strtotime($values['date_start']) > strtotime($values['date_end'])) {
                    $date_end = $this->getElement('date_end');
                    $date_end->addError($this->_messageTemplates[self::ERR_END_IS_BEFORE_START]);
                    $result = false;
                }
            }

            return $result;
        }
    }

    public function isCancelled($values)
    {
        return (isset($values['cancel']));
    }
}