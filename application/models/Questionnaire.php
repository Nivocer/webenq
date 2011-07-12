<?php
/**
 * Questionnaire class definition
 *
 * @package    Webenq
 * @subpackage Models
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Model_Questionnaire extends Webenq_Model_Base_Questionnaire
{
    public function getQuestionsAndAnswersAsArray()
    {
        return $this->toArray();
    }

    /**
     * Returns an array with the question text in the first row
     * and the answers in the following rows. Each row contains the
     * answers of one respondent.
     *
     * @return array
     */
    public function getDataAsSpreadsheetArray()
    {
        $rows = array();
        foreach ($this->QuestionnaireQuestion as $indexCol => $questionnaireQuestion) {

            // question
            $value = $questionnaireQuestion->Question->QuestionText[0]->text;
            $rows[0][$indexCol] = $value;

            // answers
            foreach ($this->Respondent as $indexRow => $respondent) {
                $answer = $respondent->Answer[$indexCol];
                if (isset($answer->text)) {
                    $value = $answer->text;
                } elseif (isset($answer->AnswerPossibility->AnswerPossibilityText[0]->text)) {
                    $value = $answer->AnswerPossibility->AnswerPossibilityText[0]->text;
                } else {
                    $value = '';
                }
                $rows[$indexRow+1][$indexCol] = $value;
            }
        }

        return $rows;
    }

    /**
     * Returns a questionnaire, based on the given id and language.
     *
     * @param int $id
     * @param string $language
     * @return Questionnaire
     */
    static public function getQuestionnaire($id, $language, $page = null,
        Webenq_Model_Respondent $respondent = null, $includeAnswers = false)
    {
        $query = Doctrine_Query::create()
            ->from('Webenq_Model_Questionnaire qe')
            ->leftJoin('qe.QuestionnaireQuestion qq');

        if ($respondent) {
            $query->leftJoin('qq.Answer a WITH a.respondent_id = ?', $respondent->id)
                ->andWhere('a.id IS NULL');
        }

        $query->leftJoin('qq.AnswerPossibilityGroup apg')
            ->leftJoin('apg.AnswerPossibility ap')
            ->leftJoin('ap.AnswerPossibilityText apt WITH apt.language = ?', $language)
            ->leftJoin('qq.Question qn')
            ->leftJoin('qn.QuestionText qt ON qn.id = qt.question_id AND qt.language = ?', $language)
            ->leftJoin('qq.CollectionPresentation cp')
            ->andWhere('qe.id = ?', $id)
            ->andWhere('cp.parent_id IS NULL')
            ->orderBy('cp.page, cp.weight, qq.id')
            ->limit(1);

        if ($page) $query->addWhere('cp.page = ?', $page);

        if ($includeAnswers) {
            $query->leftJoin('qq.Answer an')
                ->leftJoin('an.AnswerPossibility anp')
                ->leftJoin('anp.AnswerPossibilityText anpt');
        }

        if ($query->count() === 1) {
            return $query->execute()->getFirst();
        }

        return false;
    }

    /**
     * Returns the total number of pages for the current questionnaire
     *
     * @param int $id ID of the questionnaire
     * @return int Number of pages
     */
    static public function getTotalPages($id)
    {
        $result = Doctrine_Query::create()
            ->select('MAX(cp.page) as max')
            ->from('Webenq_Model_QuestionnaireQuestion qq')
            ->innerJoin('qq.CollectionPresentation cp')
            ->where('qq.questionnaire_id = ?', $id)
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        if (isset($result[0])) {
            $totalPages = (int) $result[0]['max'];
        }

        if ($totalPages > 0) {
            return $totalPages;
        }

        return 1;
    }

    /**
     * Returns the respondents for the current questionnaire
     *
     * @return Doctrine_Collection
     */
    public function getRespondents()
    {
        $result = Doctrine_Query::create()
            ->from('Webenq_Model_Respondent r')
            ->where('r.questionnaire_id = ?', $this->id)
            ->execute();

        return $result;
    }
}