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
        // get row with questions
        $row = array();
        foreach ($this->QuestionnaireQuestion as $questionnaireQuestion) {
            $this->_getQuestionCell($questionnaireQuestion, $row);
        }
        $rows = array($row);

        // get all rows with answers (on for each respondent)
        foreach ($this->Respondent as $respondent) {
            $row = array();
            foreach ($this->QuestionnaireQuestion as $questionnaireQuestion) {
                $this->_getAnswerCell($questionnaireQuestion, $respondent, $row);
            }
            $rows[] = $row;
        }

        return $rows;
    }

    protected function _getQuestionCell(Webenq_Model_QuestionnaireQuestion $parent, array &$row)
    {
        $subQuestions = Webenq_Model_QuestionnaireQuestion::getSubQuestions($parent);
        if ($subQuestions->count() > 0) {
            foreach ($subQuestions as $subQuestion) {
                $this->_getQuestionCell($subQuestion, $row);
            }
        } else {
            $row[] = $parent->Question->QuestionText[0]->text;
        }
    }

    protected function _getAnswerCell(Webenq_Model_QuestionnaireQuestion $parent,
        Webenq_Model_Respondent $respondent, array &$row)
    {
        $subQuestions = Webenq_Model_QuestionnaireQuestion::getSubQuestions($parent);
        if ($subQuestions->count() > 0) {
            foreach ($subQuestions as $subQuestion) {
                $this->_getAnswerCell($subQuestion, $respondent, $row);
            }
        } else {
            $value = '';
            $answer = $respondent->getAnswer($parent);
            if ($answer instanceof Webenq_Model_Answer) {
                if ((int) $answer->answerPossibility_id > 0) {
                    $value = $answer->AnswerPossibility->getAnswerPossibilityText()->text;
                } elseif ($answer && $answer->text !== '') {
                    $value = $answer->text;
                }
            }
            $row[] = $value;
        }
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
            ->leftJoin('qe.QuestionnaireQuestion qq')
            ->leftJoin('qq.Question qn');

        if ($respondent) {
            $query->leftJoin('qq.Answer a WITH a.respondent_id = ?', $respondent->id);
        }

        $query
//            ->leftJoin('qq.AnswerPossibilityGroup apg')
//            ->leftJoin('apg.AnswerPossibility ap')
//            ->leftJoin('ap.AnswerPossibilityText apt WITH apt.language = ?', $language)
//            ->leftJoin('qn.QuestionText qt WITH qt.language = ?', $language)
            ->leftJoin('qq.CollectionPresentation cp')
            ->where('qe.id = ?', $id)
            ->andWhere('cp.parent_id IS NULL')
            ->orderBy('cp.page, cp.weight, qq.id');

        if ($page) $query->addWhere('cp.page = ?', $page);

        if ($includeAnswers) {
            if ($respondent) {
                $query->leftJoin('a.AnswerPossibility anp')
                    ->leftJoin('anp.AnswerPossibilityText anpt');
            } else {
                $query->leftJoin('qq.Answer a')
                    ->leftJoin('a.AnswerPossibility anp')
                    ->leftJoin('anp.AnswerPossibilityText anpt');
            }
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

    public static function getCurrentPage(Webenq_Model_Questionnaire $questionaire,
        Webenq_Model_Respondent $respondent)
    {
        $qqs = Doctrine_Query::create()
            ->from('Webenq_Model_QuestionnaireQuestion qq')
            ->leftJoin('qq.Answer a ON a.questionnaire_question_id = qq.id AND a.respondent_id = ?',
                $respondent->id)
            ->innerJoin('qq.CollectionPresentation cp')
            ->where('a.id IS NULL')
            ->andWhere('qq.questionnaire_id = ?', $questionaire->id)
            ->orderBy('cp.page, cp.weight')
            ->groupBy('cp.page')
            ->limit(1)
            ->execute();

        if ($qqs->count() > 0) {
            $qq = $qqs[0];
        }

        $cp = $qq->CollectionPresentation[0];
        while ($cp->id) {
            $cp = $cp->Parent;
        }

        return $cp->page;
    }

    public function getTotalQuestions()
    {
        return (int) Doctrine_Query::create()
            ->select('COUNT(qq.id) AS count')
            ->from('Webenq_Model_QuestionnaireQuestion qq')
            ->where('qq.questionnaire_id = ?', $this->id)
            ->execute()->getFirst()->count;
    }

    public function getAnsweredQuestions(Webenq_Model_Respondent $respondent)
    {
        return (int) Doctrine_Query::create()
            ->select('COUNT(qq.id) AS count')
            ->from('Webenq_Model_QuestionnaireQuestion qq')
            ->leftJoin('qq.Answer a ON a.questionnaire_question_id = qq.id AND a.respondent_id = ?', $respondent->id)
            ->where('a.id IS NOT NULL')
            ->andWhere('qq.questionnaire_id = ?', $this->id)
            ->execute()->getFirst()->count;
    }

    /**
     * Returns an xform
     *
     * @return DOMDocument
     */
    public function getXform()
    {
        $xml = new DOMDocument('1.0', 'utf-8');
        $xml->formatOutput = true;

        $html = $xml->createElementNS('http://www.w3.org/1999/xhtml', 'h:html');
        $html->setAttribute('xlsns', 'http://www.w3.org/2002/xforms');
//        $html->setAttribute('xlsns:ev', 'http://www.w3.org/2001/xml-events');
//        $html->setAttribute('xlsns:jr', 'http://openrosa.org/javarosa');
//        $html->setAttribute('xlsns:xsd', 'http://www.w3.org/2001/XMLSchema');
        $xml->appendChild($html);

        // generate head
        $head = $xml->createElement('h:head');
        $html->appendChild($head);
        $title = $xml->createElement('h:title', Webenq::Xmlify($this->title));
        $head->appendChild($title);

        $model = $xml->createElement('model');
        $head->appendChild($model);

        $instance = $xml->createElement('instance');
        $model->appendChild($instance);

        $questionnaire = $xml->createElement(Webenq::Xmlify('questionnaire', 'tag'));
        $questionnaire->setAttribute('id', Webenq::Xmlify($this->title . ' ' . date('YmdHis'), 'attr'));
        $instance->appendChild($questionnaire);

        foreach ($this->QuestionnaireQuestion as $qq) {
            $questionnaire->appendChild($qq->getXformInstanceElement($xml));
        }

        foreach ($this->QuestionnaireQuestion as $qq) {
            $elms = $qq->getXformBindElements($xml);
            foreach ($elms as $elm) {
                $model->appendChild($elm);
            }
        }

        // generate body
        $body = $xml->createElement('h:body');
        $html->appendChild($body);

        foreach ($this->QuestionnaireQuestion as $qq) {
            $body->appendChild($qq->getXformElement($xml));
        }

        return $xml;
    }

    /**
     * Returns xform data
     *
     * @return DOMDocument
     */
    public function getXformData()
    {
        $xml = new DOMDocument('1.0', 'utf-8');
        $xml->formatOutput = true;

        $root = $xml->createElement('respondenten');
        $xml->appendChild($root);

        foreach ($this->Respondent as $respondent) {
            // respondent
            $r = $xml->createElement('respondent');
            $r->setAttribute('id', $respondent->id);
            $root->appendChild($r);

            // questionnaire
            $qn = $xml->createElement(Webenq::Xmlify('questionnaire', 'tag'));
            $qn->setAttribute('id', Webenq::Xmlify($this->title . ' ' . date('YmdHis'), 'attr'));
            $r->appendChild($qn);

            // answers
            foreach ($this->QuestionnaireQuestion as $qq) {
                $elm = $qq->getXformData($respondent, $xml);
                $qn->appendChild($elm);
            }
        }
        return $xml;
    }
}