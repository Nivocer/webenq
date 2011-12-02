<?php
/**
 * Class definition for the closed question data type scale
 */
class Webenq_Model_Question_Closed_Scale_Three extends Webenq_Model_Question_Closed_Scale
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
     * @param string $language
     * @return bool True if is this type, false otherwise
     */
    static public function isType(Webenq_Model_Question $question, $language)
    {
        /* any values? */
        if ($question->countUnique() == 0) {
            return false;
        }

        /* more than five unique values? */
        if ($question->countUniqueExcludingNullValues() > 3) {
            return false;
        }

        /* are all values present in an answer-possibility-group? */
        $group = Webenq_Model_AnswerPossibilityGroup::findByUniqueValues($question->getUniqueValues(), $language);
        if (!$group) {
            return false;
        }

        /* does it include other values than defined for this type? */
//        if ($question->otherValuesThanDefinedValid()) {
//            return false;
//        }

        return true;
    }
}