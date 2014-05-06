<?php
/**
 * Class definition for closed question data types
 */
class Webenq_Model_Question_Closed extends Webenq_Model_Base_Question_Closed
{
    /**
     * Child classes
     *
     * @var array $children
     */
    public $children = array('Scale', 'Percentage');

    /**
     * Checks if the given result set validates for this type
     *
     * @param Webenq_Model_Question $question Question containing the answervalues to test against
     * @param string $language
     * @return bool True if is this type, false otherwise
     * @todo make this numbers configurable
     */
    static public function isType(Webenq_Model_Question $question, $language)
    {
        /* any values? */
        if ($question->countUnique() == 0) {
            return false;
        }

        /* not too many different answers? */
        if ($question->countUnique() > 10) {
            return false;
        }

        /* not too many different answers (absolute and relative to number of answers)? */
        if ($question->countUnique() > 7 && $question->countUnique() / $question->count() > .333) {
            return false;
        }

        /* not too much difference in length of answers? */
        if ($question->diffLen() > 100) {
            return false;
        }

        return true;
    }
}
