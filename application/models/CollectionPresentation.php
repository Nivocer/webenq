<?php
/**
 * CollectionPresentation
 *
 * @package    Webenq
 * @subpackage Models
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Model_CollectionPresentation extends Webenq_Model_Base_CollectionPresentation
{
    public function setUp()
    {
        parent::setUp();
        $this->hasMany(
            'Webenq_Model_CollectionPresentation as Children',
            array(
                'local' => 'parent_id',
                'foreign' => 'id',
            )
        );
        $this->hasOne(
            'Webenq_Model_CollectionPresentation as Parent',
            array(
                'local' => 'id',
                'foreign' => 'parent_id',
            )
        );
    }

    /**
     * Sets the defaults based on the given questionnaire question
     *
     * @param Webenq_Model_QuestionnaireQuestion $questionnaireQuestion
     * @return void
     */
    public function setDefaults(Webenq_Model_QuestionnaireQuestion $questionnaireQuestion)
    {
        switch ($questionnaireQuestion->type) {
            case 'open':
                $this->type = Webenq::COLLECTION_PRESENTATION_OPEN_TEXT;
                break;
            case 'single':
                $this->type = Webenq::COLLECTION_PRESENTATION_SINGLESELECT_DROPDOWNLIST;
                break;
            case 'multiple':
                $this->type = Webenq::COLLECTION_PRESENTATION_MULTIPLESELECT_CHECKBOXES;
                break;
            case 'hidden':
                $this->type = Webenq::COLLECTION_PRESENTATION_OPEN_TEXT;
                break;
            default:
                throw new Exception('No question type set!');
        }
    }

    /**
     * Returns the parent of the current collection presentation object, or false
     * if there isn't one
     *
     * @return Webenq_Model_CollectionPresentation
     */
    public function getParent()
    {
        return Doctrine_Core::getTable('Webenq_Model_CollectionPresentation')
            ->find($this->parent_id);
    }

    /**
     * Returns the children of the current collection presentation object
     *
     * @return Doctrine_Collection
     */
    public function getChildren()
    {
        return Doctrine_Core::getTable('Webenq_Model_CollectionPresentation')
            ->findByParent_id($this->id);
    }

    /**
     * Returns an array of all the ancestors of the current collection presentation
     * object, or an empty array if there aren't any
     *
     * @return array
     */
    public function getParents(array $parents = array())
    {
        $parent = $this->getParent();
        if ($parent) {
            $parents[] = $parent;
            $parent->getParents($parents);
        }
        return $parents;
    }
}
