<?php

/**
 * Class definition for the closed question data type percentage
 */
class Webenq_Model_Question_Closed_Percentage extends Webenq_Model_Question_Closed
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
        /* are values numeric only? */
        if ($question->isNumeric()) {
            return false;
        }

        /* no repeating values? */
        if ($question->count() === $question->countUnique()) {
            return false;
        }

        return true;
    }
}