<?php
/**
 * Class definition for the closed question data type scale
 */
class Webenq_Model_Question_Closed_Scale_Six extends Webenq_Model_Question_Closed_Scale
{
    /**
     * Child classes
     *
     * @var array $children
     */
    public $children = array();

    /**
     * Checks if the given result set validates for this type
     *
     * @param Webenq_Model_Question $question Question containing the answervalues to test against
     * @return bool True if is this type, false otherwise
     */
    static public function isType(Webenq_Model_Question $question)
    {
        /* any values? */
        if ($question->countUnique() == 0) {
            return false;
        }

        /* more than five unique values? */
        if ($question->countUniqueExcludingNullValues() > 6) {
            return false;
        }

        /* are all values present in an answer-possibility-group? */
        $group = AnswerPossibilityGroup::findByUniqueValues($question->getUniqueValues());
        if ($group->AnswerPossibility[0]->value != 6) {
            return false;
        }

        /* does it include other values than defined for this type? */
//        if ($question->otherValuesThanDefinedValid()) {
//            return false;
//        }

        return true;
    }
}