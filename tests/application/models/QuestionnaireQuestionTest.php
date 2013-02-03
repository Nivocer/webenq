<?php
/**
 * Webenq
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
 * @package    Webenq_Tests
 * @copyright  Copyright (c) 2012 Nivocer B.V. (http://www.nivocer.com)
 * @license    http://www.gnu.org/licenses/agpl.html
 */

/**
 * @package    Webenq_Tests
 */
class Webenq_Test_Model_QuestionnaireQuestionTest extends Webenq_Test_Case_Model
{
    public $setupDatabase = true;

    public function testFormElementIsGenerated()
    {
        $questionnaire = new Webenq_Model_Questionnaire();
        $questionnaire->save();

        $question = new Webenq_Model_Question();
        $question->save();

        $questionnaireQuestion = new Webenq_Model_QuestionnaireQuestion();
        $questionnaireQuestion->Questionnaire = $questionnaire;
        $questionnaireQuestion->Question = $question;
        $questionnaireQuestion->save();

        $element = $questionnaireQuestion->getFormElement();
        $this->assertTrue($element instanceof Zend_Form_Element);
    }
}