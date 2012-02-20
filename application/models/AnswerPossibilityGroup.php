<?php
/**
 * Webenq_Model_AnswerPossibilityGroup class definition
 *
 * @package    Webenq
 * @subpackage Models
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Model_AnswerPossibilityGroup extends Webenq_Model_Base_AnswerPossibilityGroup
{
    /**
     * Gets all groups for use as multioptions in a form
     *
     * @return array
     */
    static public function getAll()
    {
        $groups = Doctrine_Query::create()
            ->from('Webenq_Model_AnswerPossibilityGroup')
            ->orderBy('name')
            ->execute();

        $retVal = array();
        foreach ($groups as $group) {
            $retVal[$group->id] = $group->name;
        }

        return $retVal;
    }

    /**
     * Returns the answer possibilities for the current group, ordered by value and label
     *
     * @return Doctrine_Collection
     */
    public function getAnswerPossibilities()
    {
        return Doctrine_Query::create()
            ->from('Webenq_Model_AnswerPossibility ap')
            ->innerJoin('ap.AnswerPossibilityText apt')
            ->where('ap.answerPossibilityGroup_id = ?', $this->id)
            ->orderBy('ap.value, apt.text')
            ->execute();
    }

    public function findAnswerPossibility($answerText, $currentLanguage = null)
    {
        $answerText = strtolower(trim($answerText));

        $possibility = null;
        // try to find answerpossibility in current group
        //@todo if we have a lot of answerpossibilities it takes a while till we find the right one.
        foreach ($this->AnswerPossibility as $possibility) {
            // try to find text in current possibility
            foreach ($possibility->AnswerPossibilityText as $answerPossibilityText) {
                if ($currentLanguage) {
                    if ($answerPossibilityText->language === $currentLanguage &&
                        $answerPossibilityText->text === $answerText)
                    {
                        return $possibility;
                    }
                    // try to find synonym in current language
                    foreach ($answerPossibilityText->AnswerPossibilityTextSynonym as $answerPossibilityTextSynonym) {
                        if ($answerPossibilityTextSynonym->text === $answerText) {
                            return $possibility;
                        }
                    }
                } else {
                    if ($answerPossibilityText->text === $answerText) {
                        return $possibility;
                    }
                    // try to find synonym in current language
                    foreach ($answerPossibilityText->AnswerPossibilityTextSynonym as $answerPossibilityTextSynonym) {
                        if ($answerPossibilityTextSynonym->text === $answerText) {
                            return $possibility;
                        }
                    }
                }
            }
        }
    }

    public function addAnswerPossibility($answerText, $language)
    {
        $answerText = strtolower($answerText);

        // check if answer-text is null value
        $nullValues = Webenq_Model_AnswerPossibilityNullValue::getNullValues();
        foreach ($nullValues as $nullValue) {
            if ($answerText === $nullValue) {
                return false;
            }
        }

        // create new answer-possibility
        $answerPossibilityText = new Webenq_Model_AnswerPossibilityText();
        $answerPossibilityText->text = $answerText;
        $answerPossibilityText->language = $language;

        $answerPossibility = new Webenq_Model_AnswerPossibility();
        $answerPossibility->AnswerPossibilityText[] = $answerPossibilityText;

        $this->AnswerPossibility[] = $answerPossibility;

        return $answerPossibility;
    }

    /**
     * Finds an existing group of answer possibilities, based on a set
     * of unique values.
     *
     * @param array $uniqueValues
     * @param string $language
     * @return Webenq_Model_AnswerPossibilityGroup
     */
    static public function findByUniqueValues($uniqueValues, $language)
    {
        // remove null values
        $nullValues = Webenq_Model_AnswerPossibilityNullValue::getNullValues();
        foreach ($nullValues as $nullValue) {
            while ($key = array_search($nullValue, $uniqueValues)) {
                unset($uniqueValues[$key]);
            }
        }

        // remove empty values
        foreach ($uniqueValues as $key => $value) {
            if (empty($value)) unset($uniqueValues[$key]);
        }

        // search possibility synonyms
        $synonyms = Doctrine_Query::create()
            ->from('Webenq_Model_AnswerPossibilityTextSynonym s')
            ->innerJoin('s.AnswerPossibilityText t WITH t.language = ?', $language)
            ->whereIn('s.text', $uniqueValues)
            ->execute();

        // search possibilities
        $texts = Doctrine_Query::create()
            ->from('Webenq_Model_AnswerPossibilityText t')
            ->where('t.language = ?', $language)
            ->andWhereIn('t.text', $uniqueValues)
            ->execute();

        // return if nothing found
        if ($synonyms->count() === 0 && $texts->count() === 0) {
            return false;
        }

        // combine results
        $combined = array();
        foreach ($synonyms as $synonym) {
            $groupId = $synonym->AnswerPossibilityText->AnswerPossibility->answerPossibilityGroup_id;
            if (key_exists($groupId, $combined)) {
                $combined[$groupId][] = $synonym->text;
            } else {
                $combined[$groupId] = array($synonym->text);
            }
        }
        foreach ($texts as $text) {
            $groupId = $text->AnswerPossibility->answerPossibilityGroup_id;
            if (key_exists($groupId, $combined)) {
                $combined[$groupId][] = $text->text;
            } else {
                $combined[$groupId] = array($text->text);
            }
        }

        // check if number of given values fits in number of found values
        $countUniqueValues = count($uniqueValues);
        foreach ($combined as $groupId => $group) {
            if ($countUniqueValues > count($group)) {
                unset($combined[$groupId]);
            }
        }

        // return false if no groups left
        if (count($combined) === 0) return false;

        // return group if just one left
        if (count($combined) === 1) {
            reset($combined);
            $groupId = key($combined);
            $group = Doctrine_Core::getTable('Webenq_Model_AnswerPossibilityGroup')
                ->find($groupId);
            return $group;
        }

        // count items per group
        $counted = array();
        foreach ($combined as $groupId => $group) {
            $counted[$groupId] = Doctrine_Query::create()
                ->from('Webenq_Model_AnswerPossibility p')
                ->innerJoin('p.AnswerPossibilityGroup g WITH g.id = ?', $groupId)
                ->count();
        }

        // sort by value, keep index
        asort($counted);

        // get best group
        $groupId = key($counted);
        $group = Doctrine_Core::getTable('Webenq_Model_AnswerPossibilityGroup')
            ->find(key($counted));
        return $group;

        return false;
    }

    /**
     * Finds an existing group of answer possibilities, based on a set
     * of answer values.
     *
     * @param array $values
     * @param string $language
     * @return Webenq_Model_AnswerPossibilityGroup
     */
    static public function findByAnswerValues($values, $language)
    {
        $values = array_map('strtolower', $values);
        $values = array_map('trim', $values);
        $uniqueValues = array_unique($values);
        return self::findByUniqueValues($uniqueValues, $language);
    }

    /**
     * Creates a new group of answer possibilities based on the provided
     * set of unique answers
     *
     * @param array $uniqueValues
     * @return self
     */
    static public function createByUniqueValues($uniqueValues, $language)
    {
        $answerPossibilityGroup = new Webenq_Model_AnswerPossibilityGroup();

        foreach ($uniqueValues as $key => $value) {
            if (empty($value)) {
                unset($uniqueValues[$key]);
            } else {
                $answerPossibility = new Webenq_Model_AnswerPossibility();
                $answerPossibility->AnswerPossibilityText[0]->text = $value;
                $answerPossibility->AnswerPossibilityText[0]->language = $language;
                $answerPossibilityGroup->AnswerPossibility[] = $answerPossibility;
            }
        }

        if ($answerPossibilityGroup->AnswerPossibility->count() > 0) {
            $answerPossibilityGroup->name = substr(implode(' / ', $uniqueValues), 0, 50);
            $answerPossibilityGroup->save();
            return $answerPossibilityGroup;
        }

        return false;
    }

    /**
     * Creates a new group of answer possibilities based on the provided
     * set of answer answers
     *
     * @param array $values
     * @param string $language
     * @return self
     */
    static public function createByAnswerValues($values, $language)
    {
        $values = array_map('strtolower', $values);
        $values = array_map('trim', $values);
        $uniqueValues = array_unique($values);
        return self::createByUniqueValues($uniqueValues, $language);
    }
}