<?php
/**
 * Form class
 *
 * @package     Webenq
 * @subpackage  Forms
 * @author      Jaap-Andre de Hoop <j.dehoop@nivocer.com>
 */
class Webenq_Form_Category_Edit extends Webenq_Form_Category_Add
{
    /**
     * Category instance
     *
     * @var array $category
     */
    protected $_category;

    /**
     * Constructor
     *
     * @param Category $category
     * @param mixed $options
     */
    public function __construct(Webenq_Model_Category $category, $options = null)
    {
        $this->_category = $category;
        parent::__construct($options);
    }

    /**
     * Initialises the form
     *
     * @return void
     */
    public function init()
    {
        $this->setName(get_class($this));
//        $this->addElements(array(
//            $this->createElement('hidden', 'id'),
//        ));
        parent::init();
        $this->setDefaults($this->_category->toArray());
    }

    public function setDefaults(array $values)
    {
        if (isset($values['CategoryText'])) {
            foreach ($values['CategoryText'] as $translation) {
                $this->getElement($translation['language'])->setValue($translation['text']);
            }
        }
    }
}