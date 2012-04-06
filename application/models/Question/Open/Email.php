<?php
/**
 * Class definition for the open question data type email
 */
class Webenq_Model_Question_Open_Email extends Webenq_Model_Base_Question_Open_Email
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
        $values = $question->getAnswerValues();

        if (count($values) === 0) {
            return false;
        }

        $validEmailAddress = new Zend_Validate_EmailAddress;
        $validValues = 0;
        $emptyValues = 0;
//TODO performance check only distinct emailadresses
        foreach ($values as $value) {
            if ($validEmailAddress->isValid($value)) {
                $validValues++;
            } else {
                if ($value === '') {
                    $emptyValues++;
                } else {
                    return false;
                }
            }
        }

        if (count($values) !== ($emptyValues + $validValues)) {
            return false;
        }

        if (count($values) === $emptyValues) {
            return false;
        }

        return true;
    }
}