<?php
/**
 * The default importer class.
 *
 * Expects the importing document to contain one
 * working sheet (set of data).
 *
 * @package     Webenq
 * @author      Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Import_Default extends Webenq_Import_Abstract
{
	/**
	 * Questionnaire model
	 *
	 * @var Webenq_Model_Questionnaire
	 */
	protected $_questionnaire;

	/**
	 * Runs the import process
	 *
	 * @return void
	 */
	public function import()
	{
		$this->_storeQuestionsAndAnswers()->_storeMeta();
	}

	/**
	 * Stores the questions
	 *
	 * @return self
	 */
	protected function _storeQuestionsAndAnswers()
	{
        // get data in spreadsheet format
        $data = $this->_adapter->getData();
        $firstWorkSheet = $data[0];
        $questionsAndAnswers = $this->_getDataAsAnswers($firstWorkSheet);

	    // create new questionnaire
		$questionnaire = $this->_questionnaire = new Webenq_Model_Questionnaire();

		// get respondent objects
        $respondents = $this->_getRespondents($questionsAndAnswers);

        // get questionnaire-questions objects
        $questionnaireQuestions = $this->_getQuestionnaireQuestions($questionsAndAnswers, $this->_language);

        // add answers to questions
        $indexQuestion = 0;
        foreach ($questionsAndAnswers as $question => $answers) {

            // get current questionnaire-question
            $questionnaireQuestion = $questionnaireQuestions[$indexQuestion];

            // save answer texts or possibilities
            if ($questionnaireQuestion->Question instanceof Webenq_Model_Question_Open) {
                foreach ($answers as $indexAnswer => $answerText) {
                    // create answer object
                    $answer = new Webenq_Model_Answer();
                    $answer->text = $answerText;
                    // connect respondent to answer
                    $answer->Respondent = $respondents[$indexAnswer];
                    // connect answer to questionnaire-question
                    $questionnaireQuestion->Answer[$indexAnswer] = $answer;
                }

            } elseif ($questionnaireQuestion->Question instanceof Webenq_Model_Question_Closed) {

                // set answer-possibility-group if not set yet
                $answerPossibilityGroup = $questionnaireQuestion->AnswerPossibilityGroup;
                if (!$answerPossibilityGroup) {
                    // find answer-possibility-group
                    $answerPossibilityGroup = Webenq_Model_AnswerPossibilityGroup::findByAnswerValues($answers, $this->_language);
                    if (!$answerPossibilityGroup) {
                        // create answer-possibility-group
                        $answerPossibilityGroup = Webenq_Model_AnswerPossibilityGroup::createByAnswerValues($answers, $this->_language);
                    }
                }

                // store answers
                foreach ($answers as $indexAnswer => $answerText) {

                    // cleanup answer
                    $answerText = preg_replace('/\s{2,}/', ' ', $answerText);

                    // ignore empty answers
                    if ($answerText === '') continue;

                    // get or create answer possibility
                    $answerPossibility = $answerPossibilityGroup->findAnswerPossibility($answerText, $this->_language);
                    if (!$answerPossibility) {
                        $answerPossibility = $answerPossibilityGroup->addAnswerPossibility($answerText, $this->_language);
                    }

                    if ($answerPossibility) {
                        // create answer object
                        $answer = new Webenq_Model_Answer();
                        $answer->AnswerPossibility = $answerPossibility;
                        // connect respondent to answer
                        $answer->Respondent = $respondents[$indexAnswer];
                        // connect answer to questionnaire-question
                        $questionnaireQuestion->Answer[$indexAnswer] = $answer;
                    }
                }
            } else {
                throw new Exception('Unknown question type!');
            }
            $indexQuestion++;
        }

        // save questionnaire
        $questionnaire->save();

		return $this;
	}

	/**
	 * Stores the filename and timestamp of upload
	 *
	 * @return self
	 */
	protected function _storeMeta()
	{
		/* get filename */
    	$filenameParts = preg_split("#/#", $this->_adapter->getFilename());
		$filename = array_pop($filenameParts);

		/* combine existing with new meta information */
		$meta = array();
		if ($this->_questionnaire->meta) {
			$meta = unserialize($this->_questionnaire->meta);
		}
		$meta['filename'] = $filename;
		$meta['timestamp'] = time();

		/* store to db */
		$this->_questionnaire->meta = serialize($meta);
		$this->_questionnaire->save();

		return $this;
	}

    /**
     * Return a collection of questionnaire-questions based on the
     * provided array with questions and ansers
     *
     * @param array $questionsAndAnswers
     * @param string $language
     * @return Doctrine_Collection Collection of questionnaire-question objects
     */
    protected function _getQuestionnaireQuestions(array $questionsAndAnswers, $language)
    {
        // get number of questions
        $count = count($questionsAndAnswers);

        // get question texts
        $questionTexts = array_keys($questionsAndAnswers);

        // create questionnaire-question objects
        for ($i=0; $i<$count; $i++) {
            $questionnaireQuestion = new Webenq_Model_QuestionnaireQuestion();

            // get and cleanup answers
            $answers = $questionsAndAnswers[$questionTexts[$i]];
            array_map('strtolower', $answers);
            array_map('trim', $answers);
            $answers = preg_replace('/\s{2,}/', ' ', $answers);

            // factor correct question type (based on given answers)
            $question = Webenq_Model_Question::factory($answers, $language);
            $question->addQuestionText($this->_language, $questionTexts[$i]);
            $questionnaireQuestion->Question = $question;
            $question->save();

            // set default question type
            if ($question instanceof Webenq_Model_Question_Closed) {
                $questionnaireQuestion->type = 'single';
            } else {
                $questionnaireQuestion->type = 'open';
            }

            // find and connect a matching answer possibility group
            if ($question instanceof Webenq_Model_Question_Closed) {
                $answerPossibilityGroup = Webenq_Model_AnswerPossibilityGroup::findByAnswerValues($answers, $this->_language);
                if (!$answerPossibilityGroup) {
                    $answerPossibilityGroup = Webenq_Model_AnswerPossibilityGroup::createByAnswerValues($answers, $this->_language);
                }
                if ($answerPossibilityGroup) {
                    $questionnaireQuestion->AnswerPossibilityGroup = $answerPossibilityGroup;
                }
            }

            // set defaults for collection-presentation
            $collectionPresentation = new Webenq_Model_CollectionPresentation();
            $collectionPresentation->setDefaults($questionnaireQuestion);
            $questionnaireQuestion->CollectionPresentation[] = $collectionPresentation;

            // add meta data
            $questionnaireQuestion->meta = serialize(array(
                'class' => get_class($question),
                'valid' => $question->getValidTypes(),
                'invalid' => $question->getInvalidTypes(),
            ));

            // connect question to questionnaire
            $this->_questionnaire->QuestionnaireQuestion[$i] = $questionnaireQuestion;
        }

        return $this->_questionnaire->QuestionnaireQuestion;
    }

	/**
	 * Calculates the number of answers and returns a colllection of
	 * respondent obecjts corresponding with the number of answers.
	 *
	 * @param array $questionsAndAnswers
	 * @return Doctrine_Collection Collection of respondent objects
	 */
	protected function _getRespondents(array $questionsAndAnswers)
	{
	    // get maximum number of responses
	    $maxAnswers = 0;
	    foreach ($questionsAndAnswers as $question => $answers) {
	        $count = count($answers);
	        if ($count > $maxAnswers) $maxAnswers = $count;
	    }

        // create respondent objects
	    for ($i=0; $i<$maxAnswers; $i++) {
	        $this->_questionnaire->Respondent[$i] = new Webenq_Model_Respondent();
        }

        return $this->_questionnaire->Respondent;
	}
}