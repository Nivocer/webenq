<?php
/**
 * WebEnq4
 *
 *  LICENSE
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    Webenq_Models
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * Questionnaire class definition
 *
 * @package    Webenq_Models
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Model_Questionnaire extends Webenq_Model_Base_Questionnaire
{
    /**
     * Returns an array with ids as keys and titles as values
     *
     * If a language is provided the corresponding translation of the
     * title is used. If not provided, the current language is used. Else
     * one of the preferred languages is used. Otherwise just any language.
     *
     * @param string $language
     * @return array
     */
    public static function getKeyValuePairs($language = null)
    {
        $questionnaires = Doctrine_Query::create()
            ->from('Webenq_Model_Questionnaire')
            ->execute();

        $pairs = array();
        foreach ($questionnaires as $questionnaire) {
            $pairs[$questionnaire->id] = $questionnaire->getTitle($language);
        }
        return $pairs;
    }

    public function getQuestionsAndAnswersAsArray()
    {
        return $this->toArray();
    }

   /**
    * Gets the questionnaire title in the given, current or preferred language. Creates
    * an empty translation if nothing was found and the questionnaire exists in the
    * database.
    *
    * @param string $language
    * @return string Title or empty string
    */
    public function getTitle($language = null)
    {
        // get curren language if not given
        if (!$language) {
            $language = Zend_Registry::get('Zend_Locale')->getLanguage();
        }

        if (isset($this->Translation[$language])) {
           return $this->Translation[$language]->title;
        }

        // return the first preferred language that is set
        $preferredLanguages = Zend_Registry::get('preferredLanguages');
        foreach ($preferredLanguages as $lang) {
            if (isset($this->Translation[$lang])) {
                return $this->Translation[$lang]->title;
            }
        }

        // return any found language
        if (count($this->Translation) > 0) {
            return $this->Translation[0]->title;
        }

        // nothing, return empty string
        return '';
    }

    /**
     * Get the published state of a questionnaire
     *
     * active is defined as: the questionnaire is activated and current time is between start and end date
     *
     * @return array
     */

    public function getPublishedState()
    {
        if ($this->active == 1) {
            $publishedState['activated']=true;
        }else {
            $publishedState['activated']=false;
        }
        $dateStart=new Zend_Date($this->date_start);

        if ($dateStart->isEarlier(time())) {
            $publishedState['afterStart']=true;
        }else{
            $publishedState['afterStart']=false;
        }
        $dateEnd=new Zend_Date($this->date_end);
        if ($dateEnd->isLater(time())) {
            $publishedState['beforeEnd']=true;
        }else {
            $publishedState['beforeEnd']=false;
        }
        if ($publishedState['activated'] && $publishedState['afterStart'] && $publishedState['beforeEnd']) {
            $publishedState['published']=true;
        }else {
            $publishedState['published']=false;
        }

        if ($this->Category->active==1){
            $publishedState['categoryActivated']=true;
        }else {
            $publishedState['published']=false;
        }

        return $publishedState;
    }

    /**
     * Sets the questionnaire title for a given language
     *
     * @param string $language The language to set the title for
     * @param string $title The questionnaire title for the given language
     * @return self
     */
    public function addQuestionnaireTitle($language, $title)
    {
        $this->Translation[$language]->title = $title;
        return $this;
    }

    /**
     * Sets the questionnaire titles for every language
     *
     * @param array $titles Array with language codes as keys and questionnaire titles as values
     * @return self
     */
    public function addQuestionnaireTitles(array $titles)
    {
        foreach ($titles as $language => $title) {
            $this->addQuestionnaireTitle($language, $title);
        }
    }


    /**
     * Fills questionnaire properties with data in the given array
     *
     * @param array $array
     * @param bool $deep
     * @see toArray()
     * @see Doctrine_Record::fromArray()
     */
    public function fromArray(array $array, $deep = true)
    {
        parent::fromArray($array, $deep);

            if (isset($array['title']) && is_array($array['title'])) {
            foreach ($array['title'] as $language => $title) {
                if ($language == 'default_language') {
                    $this->default_language = $title;
                } elseif ($title) {
                    $this->Translation[$language]->title = $title;
                }
            }
        }
    }

    /**
     * Fills array with questionnaire properties, and makes translations of the
     * field title available for form element "multi-lingual with default
     * language choice":
     *
     * <ul>
     * <li>result['title']['default_language'] maps to the property 'default_language'
     * <li>result['title']['en'] contains the English version, etc
     * </ul>
     *
     * @param bool $deep
     * @param bool $prefixKey Not used
     * @return array
     * @see fromArray()
     * @see Doctrine_Record::fromArray()
     */
    public function toArray($deep = true, $prefixKey = false)
    {
        $result = parent::toArray($deep, $prefixKey);

        if (isset($result['default_language'])) {
            $result['title']['default_language'] = $result['default_language'];
        }

        foreach ($this->Translation as $lang=>$translation) {
            if (isset($translation->title)) {
                $result['title'][$lang] = $translation->title;
            }
        }

        return $result;
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
            $row[] = $parent->Question->getQuestionText()->text;
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

    static public function getQuestionnaires($category=null, $id=null){
        $query=Doctrine_Query::create()
        ->from('Webenq_Model_Questionnaire q')
        ->leftJoin('q.Category c')
        ->orderBy('c.weight, q.weight');
        if ($category){
            $query->andWhere('q.category_id=?',$category);
        }
        if ($id){
            $query->andWhere('q.id=?',$id);
        }
        return $query->execute();
    }
    /**
     * Returns a questionnaire, based on the given id and language.
     *
     * @param int $id
     * @param string $language @obsolete?
     * @param int $page @obsolete
     * @param Webenq_Model_Respondent $respondent @obsolete
     * @param boolean $inludeAnswers @obsolete
     * @return Webenq_Model_Questionnaire
     */

    static public function getQuestionnaire($id, $language=null, $page = null,
        Webenq_Model_Respondent $respondent = null, $includeAnswers = false)
    {
        $questionnaire=Doctrine_Core::getTable('Webenq_Model_Questionnaire')->find($id);
        return $questionnaire;
/* old code (pre hierarchical)
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
            ->leftJoin('qe.Translation qt')
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
*/
    }

    public function getLastPage(){
        $questionnaireQuestionModel=new Webenq_Model_QuestionnaireNode();
        $questionnaireRootNode=$questionnaireQuestionModel->getTable()->findById($this->questionnaire_node_id)->getFirst();
        return $questionnaireRootNode->getNode()->getLastChild();
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
            ->leftJoin(
                'qq.Answer a ON a.questionnaire_question_id = qq.id AND a.respondent_id = ?',
                $respondent->id
            )
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

    public function countAnsweredQuestions(Webenq_Model_Respondent $respondent)
    {
        return (int) Doctrine_Query::create()
            ->from('Webenq_Model_QuestionnaireQuestion qq')
            ->innerJoin('qq.Answer a WITH a.respondent_id = ?', $respondent->id)
            ->where('qq.questionnaire_id = ?', $this->id)
            ->count();
    }

    /**
     * @deprecated
     * @todo This method has been renamed to countAnsweredQuestions, so this one
     * should be removed when all calls have been renamed as well
     */
    public function getAnsweredQuestions(Webenq_Model_Respondent $respondent)
    {
        return $this->countAnsweredQuestions($respondent);
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
        $title = $xml->createElement('h:title', Webenq::Xmlify($this->getTitle()));
        $head->appendChild($title);

        $model = $xml->createElement('model');
        $head->appendChild($model);

        $instance = $xml->createElement('instance');
        $model->appendChild($instance);

        $questionnaire = $xml->createElement(Webenq::Xmlify('questionnaire', 'tag'));
        $questionnaire->setAttribute(
            'id',
            Webenq::Xmlify(
                $this->getTitle() . ' ' . date('YmdHis'),
                'attr'
            )
        );
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
            $qn->setAttribute(
                'id',
                Webenq::Xmlify($this->getTitle() . ' ' . date('YmdHis'), 'attr')
            );
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