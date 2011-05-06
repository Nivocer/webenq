<?php
/**
 * Class definition for the open question data type text
 */
class Webenq_Model_Question_Open_Text extends Webenq_Model_Question_Open
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
        return true;
    }
}