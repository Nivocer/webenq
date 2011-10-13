<?php
/**
 * Class definition for the open question data type number
 */
class Webenq_Model_Question_Open_Number extends Webenq_Model_Question_Open
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
        if (!$question->isNumeric()) {
            return false;
        }

        return true;
    }


    /**
     * Returns the sum of all values
     *
     * @return int|float Sum of values
     */
    public function sum()
    {
        return array_sum($this->getAnswerValues());
    }
}