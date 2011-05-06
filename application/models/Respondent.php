<?php
class Webenq_Model_Respondent extends Respondent
{
    /**
     * Class constructor
     *
     * Explicitly set the table and defaults to new entry
     *
     * @param Doctrine_Table $table
     * @param bool $isNewEntry
     */
    public function __construct($table = null, $isNewEntry = true)
    {
        $table = Doctrine_Core::getTable('Respondent');
        parent::__construct($table, $isNewEntry);
    }
}