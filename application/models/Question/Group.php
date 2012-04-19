<?php
/**
 * Class definition for open question data types
 */
class Webenq_Model_Question_Group extends Webenq_Model_Base_QuestionGroup
{
    /**
     * Child classes
     *
     * @var array $children
     */
    public $children = array();

    /**
     * Is group or not?
     *
     * @var bool $isGroup
     */
    public $isGroup = true;
}