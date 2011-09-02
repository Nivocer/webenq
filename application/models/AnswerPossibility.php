<?php
/**
 * AnswerPossibility class definition
 *
 * @package    Webenq
 * @subpackage Models
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Model_AnswerPossibility extends Webenq_Model_Base_AnswerPossibility
{
    /**
     * Gets the answer possibility text in the given or current language
     *
     * @param string $language
     * @return string|boolean
     */
    public function getAnswerPossibilityText($language = null)
    {
        if (!$language) {
            $language = Zend_Registry::get('Zend_Locale')->getLanguage();
        }
        foreach ($this->AnswerPossibilityText as $text) {
            if ($text->language === $language) return $text->text;
        }
        return false;
    }
}
