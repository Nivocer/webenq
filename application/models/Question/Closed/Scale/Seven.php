<?php
/**
 * Class definition for the closed question data type scale
 */
class Webenq_Model_Question_Closed_Scale_Seven extends Webenq_Model_Base_Question_Closed_Scale_Seven
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

        /* more than seven unique values? */
        if ($question->countUniqueExcludingNullValues() > 7) {
            return false;
        }

        /* does it include other values than defined for this type? */
       if ($question->otherValuesThanDefinedValid()) {
           return false;
       }

        return true;
    }
}