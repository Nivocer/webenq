<?php
/**
 * Class definition for open question data types
 */
class Webenq_Model_Question_Open extends Webenq_Model_Question
{
    /**
     * Child classes
     *
     * @var array $children
     */
    public $children = array('Email', 'Date', 'Number', 'Text');

    /**
     * Checks if the given result set validates for this type
     *
     * @param Webenq_Model_Question $question Question containing the answervalues to test against
     * @param string $language
     * @return bool True if is this type, false otherwise
     */
    static public function isType(Webenq_Model_Question $question, $language)
    {
        return true;
    }
}