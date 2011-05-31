<?php
/**
 * AnswerPossibilityGroup class definition
 */
class Webenq_Model_AnswerPossibilityGroup extends AnswerPossibilityGroup
{
    /**
     * Class constructor
     *
     * Defaults to new entry
     *
     * @param Doctrine_Table $table
     * @param bool $isNewEntry
     */
    public function __construct($table = null, $isNewEntry = true)
    {
        parent::__construct($table, $isNewEntry);
    }
}