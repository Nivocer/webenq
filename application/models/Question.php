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
 * Question class definition
 *
 * @package    Webenq_Models
 * @author     Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Model_Question extends Webenq_Model_Base_Question
{
    protected $_questionnaire;

    /**
     * Array of answer values
     *
     * Used for quickly storing answer values, i.e. by the factory
     * method. Storing all answers as Webenq_Model_Answer objects
     * would cause unnecessary overhead.
     *
     * @var array $_answerValues
     */
    protected $_answerValues = array();

    /**
     * Types that validate for the given set of values
     *
     * @var array $_validTypes
     */
    protected $_validTypes = array();

    /**
     * Types that do not validate for the given set of values
     *
     * @var array $_invalidTypes
     */
    protected $_invalidTypes = array();

    /**
     * Child classes
     *
     * @var array $children
     */
    public $children = array('Closed', 'Open');

    /**
     * Searches the question repository for the given term in the given language.
     * If no language is given, all languages are searched.
     *
     * @param string $term
     * @param string $lang
     * @return Doctrine_Collection containing instances of Question
     */
    static public function search($term, $lang = null, $limit = null)
    {
        $query = Doctrine_Query::create()
            ->from('Webenq_Model_Question q')
            ->innerJoin('q.QuestionText qt')
            ->where('qt.text LIKE ?', "%$term%");

        if ($lang) $query->andWhere('qt.language = ?', $lang);
        if ($limit) $query->limit($limit);

        return $query->execute();
    }

    static public function autocomplete($term, $lang = null, $limit = null)
    {
        $questions = self::search($term, $lang, $limit);

        $autocomplete = array();
        foreach ($questions as $question) {
            $autocomplete[] = array(
                'value' => $question->id,
                'label' => $question->QuestionText[0]->text,
            );
        }

        return $autocomplete;
    }

    /**
     * Gets all types for which this question would validate
     *
     * @return array
     */
    public function getValidTypes()
    {
        return $this->_validTypes;
    }

    /**
     * Gets all types for which this question would not validate
     *
     * @return array
     */
    public function getInvalidTypes()
    {
        return $this->_invalidTypes;
    }

    /**
     * Sets the types for which the data would validate
     *
     * @param array Array of class names
     */
    public function setValidTypes(array $types)
    {
        $this->_validTypes = array();
        $this->addValidTypes($types);
    }

    /**
     * Sets the types for which the data would not validate
     *
     * @param array Array of class names
     */
    public function setInvalidTypes(array $types)
    {
        $this->_invalidTypes = array();
        $this->addInvalidTypes($types);
    }

    public function setQuestionnaire(Questionnaire $questionnaire)
    {
        $this->_questionnaire = $questionnaire;
    }

    /**
     * Adds a collection of answers to the current question
     *
     * @param array $answers
     * @param array $respondents
     * @return self
     */
    public function addAnswers(array $answers, array $respondents = null)
    {
        foreach ($answers as $key => $answer) {
            if (isset($respondents[$key])) {
                $this->addAnswer($answer, $respondents[$key]);
            } else {
                $this->addAnswer($answer);
            }
        }

        return $this;
    }

    /**
     * Adds an answer to the current question
     *
     * @param Webenq_Model_Answer|string $answer
     * @param Respondent $respondent
     * @return self
     */
    public function addAnswer($answer, Respondent $respondent = null)
    {
        if (!$respondent) {
            $respondent = new Webenq_Model_Respondent();
            $respondent->Questionnaire = new Webenq_Model_Questionnaire();
        }

        if (is_string($answer) || is_numeric($answer) || is_null($answer)) {
            $value = $answer;
            $answer = new Webenq_Model_Answer();
            $answer->text = $value;
        } elseif (!$answer instanceof Webenq_Model_Answer) {
            throw new Exception('Parameter must be a string or an instance of Webenq_Model_Answer');
        }
        $answer->Respondent = $respondent;

        if ($this->QuestionnaireQuestion->count() == 0) {
            // save in order to have an id
//            $this->save();
            // connect to questionnaire
            $questionnaireQuestion = new Webenq_Model_QuestionnaireQuestion();
            $questionnaireQuestion->questionnaire_id = $respondent->questionnaire_id;
            $questionnaireQuestion->meta = serialize(
                array(
                    'class' => get_class($this),
                    'valid' => $this->getValidTypes(),
                )
            );
            $this->QuestionnaireQuestion[0] = $questionnaireQuestion;
        }

        // add answer object
        $this->QuestionnaireQuestion[0]->Answer[] = $answer;

        return $this;
    }

    /**
     * Returns the answer values
     *
     * @return array An array with answer values
     */
    public function getAnswerValues()
    {
        if (count($this->_answerValues) > 0) {
            return $this->_answerValues;
        }

        $answers = $this->QuestionnaireQuestion[0]->Answer->toArray();

        $values = array();
        foreach ($answers as $answer) {
            $values[] = $answer['text'];
        }

        return $values;
    }

    /**
     * Sets the answer values
     *
     * @param array $answers An array with answer values
     */
    public function setAnswerValues(array $answers = array())
    {
        array_map('strtolower', $answers);
        array_map('trim', $answers);
        $answers = preg_replace('/\s{2,}/', ' ', $answers);
        $this->_answerValues = $answers;
    }

    /**
     * Gets the question text in the given, current or preferred language
     *
     * @param string $language
     * @return Webenq_Model_QuestionText
     * @throws Exception
     */
    public function getQuestionText($language = null)
    {
        // get curren language if not given
        if (!$language) {
            $language = Zend_Registry::get('Zend_Locale')->getLanguage();
        }

        // build array with available languages
        $available = array();
        foreach ($this->QuestionText as $text) {
            $available[$text->language] = $text;
        }

        // return current language if set
        if (key_exists($language, $available)) {
            return $available[$language];
        }

        // return the first preferred language that is set
        $preferredLanguages = Zend_Registry::get('preferredLanguages');
        foreach ($preferredLanguages as $preferredLanguage) {
            if (key_exists($preferredLanguage, $available)) {
                return $available[$preferredLanguage];
            }
        }

        // return any found language
        return $this->QuestionText[0];

        // throw Exception if no translation was found
        throw new Exception(
            'No translation was found for ' . get_class($this) .
            ' with ID ' . $this->id
        );
    }

    /**
     * Sets the question text for a given language
     *
     * @param string $language The language to set the text for
     * @param string $text The question text for the given language
     * @return self
     */
    public function addQuestionText($language, $text)
    {
        if ($text) {
            $questionText = new Webenq_Model_QuestionText();
            $questionText->language = $language;
            $questionText->text = $text;
            $this->QuestionText[] = $questionText;
        }
    }

    /**
     * Sets the question texts for every language
     *
     * @param array $texts Array with language codes as keys and question texts as values
     * @return self
     */
    public function addQuestionTexts(array $texts)
    {
        foreach ($texts as $language => $text) {
            $this->addQuestionText($language, $text);
        }
    }

    /**
     * Determines the question type based on the data provided
     *
     * @param Webenq_Model_Question $callingObject The calling object
     * @param string $language
     * @return Webenq_Model_Question An instance of Webenq_Model_Question
     */
    protected function _determineType(Webenq_Model_Question $callingObject, $language)
    {
        // get answer values
        $values = $this->getAnswerValues();

        foreach ($callingObject->children as $child) {

            // create the child object
            $class = get_class($callingObject) . '_' . $child;
            $object = new $class();
            $object->setAnswerValues($values);

            // add valid and invalid types from calling object to the child object
            $object->addValidTypes($callingObject->getValidTypes());
            $object->addInvalidTypes($callingObject->getInvalidTypes());

            // if the current child object validates, continue with children if any,
            // or add this type to the array with valid types
            if (call_user_func(get_class($object).'::isType', $object, $language)) {
                if (count($object->children) > 0) {
                    $this->_determineType($object, $language);
                } else {
                    $this->addValidType(get_class($object));
                }
            } else {
                $this->addInvalidType(get_class($object));
            }
        }
        return $this;
    }

    /**
     * Factors a question object of the right type
     *
     * The provided data is used for detecting the proper question type,
     * but is not included in the returned question object because answers
     * can only be added to questions that are connected to a questionnaire
     * (which is beyond the scope of this factory).
     *
     * @param array $answers Array of answer values to test against
     * @param string $language
     * @return Webenq_Model_Question
     */
    static public function factory(array $answers, $language)
    {
        // if no answers: type defaults to open text
        if (!self::answersGiven($answers)) {
            $question = new Webenq_Model_Question_Open_Text();
            $question->created = date('Y-m-d H:i:s');
            return $question;
        }

        // determine question valid question types
        $baseQuestion = new self();
        $baseQuestion->setAnswerValues($answers);
        $question = $baseQuestion->_determineType($baseQuestion, $language);
        $validTypes = $question->getValidTypes();
        $invalidTypes = $question->getInvalidTypes();

        if (count($validTypes) === 0) {
            throw new Exception('No valid question type found!');
        }

        /**
         * Usually the first caught type is the best. However, the
         * date-type should have priority over any closed-type. An
         * extra check is needed.
         */
        if (in_array('Webenq_Model_Question_Open_Date', $validTypes)) {
            foreach ($validTypes as $validType) {
                if (preg_match('#^Webenq_Model_Question_Closed#', $validType)) {
                    array_unshift($validTypes, 'Webenq_Model_Question_Open_Date');
                    $validTypes = array_unique($validTypes);
                    break;
                }
            }
        }

        // instantiate and return question object
        $question = new $validTypes[0]();
        $question->created = date('Y-m-d H:i:s');
        $question->setValidTypes($validTypes);
        $question->setInvalidTypes($invalidTypes);
        return $question;
    }

    /**
     * Add a type that has been validated
     *
     * @param string Class names
     */
    public function addValidType($validType)
    {
        if (!in_array($validType, $this->_validTypes)) {
            $this->_validTypes[] = $validType;
        }
    }

    /**
     * Add an array of types that have been validated
     *
     * @param array Array of valid class names
     */
    public function addValidTypes(array $validTypes)
    {
        if (is_array($validTypes)) {
            foreach ($validTypes as $validType) {
                $this->addValidType($validType);
            }
        }
    }

    /**
     * Add a type that has not been validated
     *
     * @param string Class names
     */
    public function addInvalidType($invalidType)
    {
        if (!in_array($invalidType, $this->_invalidTypes)) {
            $this->_invalidTypes[] = $invalidType;
        }
    }

    /**
     * Add an array of types that have not been validated
     *
     * @param array Array of valid class names
     */
    public function addInvalidTypes(array $invalidTypes)
    {
        if (is_array($invalidTypes)) {
            foreach ($invalidTypes as $invalidType) {
                $this->addInvalidType($invalidType);
            }
        }
    }

    /**
     * Returns unique values
     *
     * @return array
     */
    public function getUniqueValues()
    {
        $answers = $this->getAnswerValues();
        array_map('strtolower', $answers);
        array_map('trim', $answers);
        return array_unique($answers);
    }

    /**
     * Returns unique values
     *
     * @return array
     */
    public function getUniqueValuesExcludingNullValues()
    {
        $answers = $this->getUniqueValues();
        $nullValues = Webenq_Model_AnswerPossibilityNullValue::getNullValues();

        foreach ($answers as $key => $val) {
            if (in_array(strtolower($val), $nullValues)) {
                unset($answers[$key]);
            }
        }

        return array_unique($answers);
    }


    /**
     * Checks if the given set of answers validates for this question type
     *
     * @param Webenq_Model_Question $question A question object containing the data to test against
     * @param string $language
     * @return bool True if is this type, false otherwise
     */
    static public function isType(Webenq_Model_Question $question, $language)
    {
        throw new Exception(__FUNCTION__ . " is not implemented");
    }

    /**
     * Finds the length of the value with the smallest lenth
     *
     * @return int Length of the value with the smallest length
     */
    public function minLen()
    {
        $answerValues = $this->getAnswerValues();

        if (!isset($answerValues[0])) {
            return false;
        }

        $minLen = strlen($answerValues[0]);

        foreach ($answerValues as $value) {
            if (strlen($value) < $minLen) {
                $minLen = strlen($value);
            }
        }

        return $minLen;
    }

    /**
     * Finds the length of the value with the biggest lenth
     *
     * @return int Length of the value with the biggest length
     */
    public function maxLen()
    {
        $answerValues = $this->getAnswerValues();

        if (!isset($answerValues[0])) {
            return false;
        }

        $maxLen = strlen($answerValues[0]);

        foreach ($answerValues as $value) {
            if (strlen($value) > $maxLen) {
                $maxLen = strlen($value);
            }
        }

        return $maxLen;
    }

    /**
     * Finds the maximum difference in length of the values
     *
     * @return int Difference in length
     */
    public function diffLen()
    {
        return $this->maxLen() - $this->minLen();
    }

    /**
     * Finds the smallest value
     *
     * @return string Smallest value
     */
    public function minVal()
    {
        $answerValues = $this->getAnswerValues();

        if (!isset($answerValues[0])) {
            return false;
        }

        $minVal = $answerValues[0];

        foreach ($answerValues as $value) {
            if ($value < $minVal) {
                $minVal = $value;
            }
        }

        return $minVal;
    }

    /**
     * Finds the biggest value
     *
     * @return string Biggest value
     */
    public function maxVal()
    {
        $answerValues = $this->getAnswerValues();

        if (!isset($answerValues[0])) {
            return false;
        }

        $maxVal = $answerValues[0];

        foreach ($answerValues as $value) {
            if ($value > $maxVal) {
                $maxVal = $value;
            }
        }

        return $maxVal;
    }

    /**
     * Counts the number of values
     *
     * @return int Number of values
     */
    public function count()
    {
        $values = $this->_answerValues;

        if (!is_array($values)) {
            return false;
        }

        foreach ($values as $key => $value) {
            if (trim($value) === '') {
                unset($values[$key]);
            }
        }

        return count($values);
    }

    /**
     * Counts the number of unique values
     *
     * @return int Number of unique values
     */
    public function countUnique()
    {
        $values = $this->getAnswerValues();

        if (!is_array($values)) {
            return false;
        }

        foreach ($values as $key => $value) {
            if (trim($value) === '') {
                unset($values[$key]);
            }
        }

        return count(array_unique($values));
    }

    /**
     * Counts the number of unique values, excluding null values
     *
     * @return int Number of unique values
     */
    public function countUniqueExcludingNullValues()
    {
        /* check for data */
        if (!is_array($this->_answerValues)) {
            return false;
        }

        /* cleanup tmp data */
        $tmpData = $this->_answerValues;
        foreach ($tmpData as $key => $val) {
            if (in_array(strtolower($val), Webenq_Model_AnswerPossibilityNullValue::getNullValues())) {
                unset($tmpData[$key]);
            }
        }

        return count(array_unique($tmpData));
    }

    /**
     * Determines whether the values are numeric values only
     *
     * @return bool True or false
     */
    public function isNumeric()
    {
        if (!is_array($this->_answerValues)) return false;

        $validator = new Zend_Validate_Regex('/^[\+-]?([0-9]*)((,|\.)[0-9]{3})*((,|\.)[0-9]*){0,1}$/');
        foreach ($this->_answerValues as $value) {
            if ($value && !$validator->isValid($value)) return false;
        }
        return true;
    }

    /**
     * Checks if at least one answer is not-empty
     *
     * @param array $values Array with values
     * @return bool True if any answers, false otherwise
     */
    static public function answersGiven($values)
    {
        if (count($values) === 0) {
            return false;
        }

        $unique = array_unique($values);
        if (count($unique) === 1 && trim($unique[0]) === '') {
            return false;
        }

        return true;
    }

    public static function findByQuestionText($questionText, $language)
    {
        $result = Doctrine_Query::create()
            ->from('Webenq_Model_Question q')
            ->innerJoin('q.QuestionText qt')
            ->where('qt.language - ?', $language)
            ->andWhere('qt.text LIKE ?', "%$questionText%")
            ->execute();

        if ($result->count() > 0) {
            return $result->getFirst();
        }
    }

    /**
     * Returns the questionnaires using this question
     *
     * @return Doctrine_Collection
     */
    public function getQuestionnaires()
    {
        return Doctrine_Query::create()
            ->from('Webenq_Model_Questionnaire q')
            ->innerJoin('q.QuestionnaireQuestion qq')
            ->where('qq.question_id = ?', $this->id)
            ->groupBy('q.id')
            ->execute();
    }

    public function preInsert($event)
    {
        $invoker = $event->getInvoker();
        $invoker->created = date('Y-m-d H:i:s');
    }


    /**
     * Stores the question to db
     *
     * @param string $text Question text
     * @param string $lang Language of question text
     * @return bool True on success, false otherwise
     */
//    public function save($text, $lang = 'nl')
//    {
//
//        var_dump(__FILE__, __LINE__, $text); die;
//        $q = @Doctrine_Query::create()
//            ->from('Webenq_Model_Question q')
//            ->innerJoin('q.QuestionText qt')
//            ->where('qt.text = ?', $text)
//            ->andWhere('qt.language = ?', $lang)
//            ->execute()
//            ->getFirst();
//
//        if (!$q) {
//            $q = new Question;
//            $q->QuestionText[0]->text = $text;
//            $q->QuestionText[0]->language = $lang;
//            $q->save();
//        }
//
//        return $q;
//    }
}