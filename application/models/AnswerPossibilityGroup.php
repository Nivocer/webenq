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
     * Finds an existing group of answer possibilities, based on a set
     * of unique values.
     *
     * @param array $uniqueValues
     * @return Webenq_Model_AnswerPossibilityGroup
     */
    static public function findByUniqueValues($uniqueValues)
    {
        /* remove null values */
        $nullValues = Webenq_Model_AnswerPossibilityNullValue::getNullValues();
        foreach ($nullValues as $nullValue) {
            while ($key = array_search($nullValue, $uniqueValues)) {
                unset($uniqueValues[$key]);
            }
        }

        /* search possibility synonyms */
        $query = Doctrine_Query::create()->from('Webenq_Model_AnswerPossibilityTextSynonym s');
        foreach ($uniqueValues as $value) $query->orWhere('s.text = ?', strtolower($value));
        $synonyms = $query->execute();

        /* search possibilities */
        $query = Doctrine_Query::create()->from('Webenq_Model_AnswerPossibilityText t');
        foreach ($uniqueValues as $value) $query->orWhere('t.text = ?', strtolower($value));
        $texts = $query->execute();

        /* combine results */
        $groupIds = array();
        foreach ($synonyms as $synonym) {
            $groupIds[] = $synonym->AnswerPossibilityText->AnswerPossibility->answerPossibilityGroup_id;
        }
        foreach ($texts as $text) {
            $groupIds[] = $text->AnswerPossibility->answerPossibilityGroup_id;
        }

        /* find most apropriate group */
        if (count($groupIds) > 0) {
            $countedGroupIds = array_count_values($groupIds);
            arsort($countedGroupIds);
            $bestGroupId = key($countedGroupIds);
            $groups = Doctrine_Query::create()
                ->from('Webenq_Model_AnswerPossibilityGroup g')
                ->innerJoin('g.AnswerPossibility p')
                ->where('g.id = ?', $bestGroupId)
                ->orderBy('p.value DESC')
                ->execute();
            if ($groups->count() > 0) return $groups->getFirst();
        }
    }

    /**
     * Creates a new group of answer possibilities based on the provided
     * set of unique answers
     *
     * @param array $uniqueValues
     * @return self
     */
    static public function createByUniqueValues($uniqueValues)
    {
        $answerPossibilityGroup = new Webenq_Model_AnswerPossibilityGroup();

        foreach ($uniqueValues as $key => $value) {
            if (!$value) {
                unset($uniqueValues[$key]);
            } else {
                $answerPossibility = new Webenq_Model_AnswerPossibility();
                $answerPossibility->AnswerPossibilityText[0]->text = $value;
                $answerPossibility->AnswerPossibilityText[0]->language = 'nl';
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
}
