<?php
/**
 * Questback importer class.
 *
 * Expects the importing document to contain three
 * working sheets (sets of data), formatted as an
 * Questback export.
 *
 * @package		Webenq
 * @author Bart Huttinga <b.huttinga@nivocer.com>
 */
class Webenq_Import_Questback extends Webenq_Import_Default
{
	/**
	 * Runs the import process
	 */
	public function import()
	{
		$this->_storeQuestionsAndAnswers()
			->_storeMeta()
			->_storeInfo()
			->_storeGroups();
	}

	/**
	 * Stores the key/value-pairs provided in the third working sheet
	 *
	 * @return self
	 */
	protected function _storeInfo()
	{
		$allData = $this->_adapter->getData();
		$data = $allData[2];

		/* iterate over data */
		$meta = array();
		foreach ($data as $row) {
			$meta[$row[0]] = $row[1];
		}

		/* get current meta, add to it, and save */
		$newMeta = isset($this->_questionnaire->meta) ? unserialize($this->_questionnaire->meta) : array();
		$newMeta += $meta;
		$this->_questionnaire->title = $data[0][1];
		$this->_questionnaire->meta = serialize($newMeta);
		$this->_questionnaire->save();

		return $this;
	}

	/**
	 * Stores the question groups
	 *
	 * @return self
	 */
	protected function _storeGroups()
	{
		$sheets = $this->_adapter->getData();

		$questionnaire = $this->_questionnaire;

		// find questions per group
        $groups = array();
		foreach ($questionnaire->QuestionnaireQuestion as $questionnaireQuestion) {
			$question = $questionnaireQuestion->Question;
			$text = $question->QuestionText[0]->text;
			if (preg_match("#^(\d+):(.*)$#", $text, $matches)) {
				$groups[$matches[1]][] = $questionnaireQuestion;
			}
		}

		// find group names
        $groupNames = array();
		foreach ($sheets[1] as $row) {
			preg_match('#^(\d*):\s*=(.*)$#', $row[0], $matches);
			$groupNames[$matches[1]] = trim($matches[2]);
		}

		// auto-create empty group names
		foreach ($groups as $i => $questions) {
		    if (!key_exists($i, $groupNames)) {
		        $groupNames[$i] = "Groep $i";
		    }
		}

        // save groups
        foreach ($groups as $id => $group) {

            // create and save parent question
            $parentQuestion = new Webenq_Model_Question_Open();
            $parentQuestion->created = date('Y-m-d H:i:s');
            $parentQuestion->QuestionText[0]->language = $this->_language;
            $parentQuestion->QuestionText[0]->text = $groupNames[$id];
            $parentQuestion->save();

            // connect parent question to current questionnaire
            $parentQuestionnaireQuestion = new Webenq_Model_QuestionnaireQuestion();
            $parentQuestionnaireQuestion->Question = $parentQuestion;
            $parentQuestionnaireQuestion->Questionnaire = $questionnaire;
            $parentQuestionnaireQuestion->CollectionPresentation[0]->setDefaults($parentQuestionnaireQuestion);
            $parentQuestionnaireQuestion->save();

            $parentId = $parentQuestionnaireQuestion->CollectionPresentation[0]->id;

            foreach ($group as $qq) {
                // remove group number from question text
                $qq->Question->QuestionText[0]->text = preg_replace('/^(\d+): /', null, $qq->Question->QuestionText[0]->text);
                $qq->Question->QuestionText[0]->save();
                // connect to parent
                $qq->CollectionPresentation[0]->parent_id = $parentId;
                $qq->CollectionPresentation[0]->save();
            }
        }

        // save groups
		foreach ($groups as $id => $group) {
			$questionGroup = new Webenq_Model_QuestionGroup();
			$questionGroup->name = (isset($groupNames[$id]) ? $groupNames[$id] : '');
			foreach ($group as $question) {
				$questionGroup->QuestionnaireQuestion[] = $question;
			}
			$questionGroup->save();
		}

		return $this;
	}
}