<?php
/**
 * Respondent
 *
 * @package    Webenq
 * @subpackage Models
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Model_Respondent extends Webenq_Model_Base_Respondent
{
    public function getAnswer(Webenq_Model_QuestionnaireQuestion $questionnaireQuestion)
    {
        $answers = Doctrine_Query::create()
            ->from('Webenq_Model_Answer a')
            ->where('a.respondent_id = ?', $this->id)
            ->andWhere('a.questionnaire_question_id = ?', $questionnaireQuestion->id)
            ->limit(1)
            ->execute();

        if (count($answers) === 1) {
            return $answers->getFirst();
        }

        return false;
    }
}