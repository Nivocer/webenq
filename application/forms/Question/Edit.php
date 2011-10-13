<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
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

        $id = $this->createElement('hidden', 'id', array(
            'value' => $this->_question->id,
        ));
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