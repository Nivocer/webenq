<?php
class Webenq_Test_Model_CollectionPresentationTest extends Webenq_Test_Case_Model
{
    /**
     * Creates a questionnaire-question and tests if the default element
     * type is set correctly.
     */
    public function testDefaultTypeIsSetCorrectly()
    {
        $qq = new Webenq_Model_QuestionnaireQuestion();
        $cp = new Webenq_Model_CollectionPresentation();

        // open
        $qq->type = 'open';
        $cp->setDefaults($qq);
        $this->assertTrue($cp->type == Webenq::COLLECTION_PRESENTATION_OPEN_TEXT);

        // single
        $qq->type = 'single';
        $cp->setDefaults($qq);
        $this->assertTrue($cp->type == Webenq::COLLECTION_PRESENTATION_SINGLESELECT_DROPDOWNLIST);

        // multiple
        $qq->type = 'multiple';
        $cp->setDefaults($qq);
        $this->assertTrue($cp->type == Webenq::COLLECTION_PRESENTATION_MULTIPLESELECT_CHECKBOXES);

        // hidden
        $qq->type = 'hidden';
        $cp->setDefaults($qq);
        $this->assertTrue($cp->type == Webenq::COLLECTION_PRESENTATION_OPEN_TEXT);

        // undefined type throws Exception
        $qq->type = 'undefind';
        try {
            $cp->setDefaults($qq);
        } catch (Exception $e) {}
        $this->assertTrue($e instanceof Exception);
    }
}