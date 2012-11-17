<?php
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
     */
    public function __construct($id, $text, $options = null)
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
}