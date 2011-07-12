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
        $this->hasMany('Webenq_Model_CollectionPresentation as CollectionPresentation', array(
            'local' => 'id',
            'foreign' => 'parent_id',
        ));
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
}
